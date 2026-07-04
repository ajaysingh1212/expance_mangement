<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'phone', 'avatar', 'cover_photo', 'bio', 'designation', 'department',
        'date_of_birth', 'gender', 'address', 'city', 'state', 'country', 'postal_code',
        'facebook', 'twitter', 'linkedin', 'instagram', 'github', 'website',
        'created_by', 'is_active', 'last_login_at', 'last_login_ip','pin','pin_enabled',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'created_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Helper Methods
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && $this->avatar !== 'default-avatar.png') {
            return asset('storage/avatars/' . $this->avatar);
        }
        return asset('images/default-avatar.png');
    }

    public function getCoverPhotoUrlAttribute(): string
    {
        if ($this->cover_photo && $this->cover_photo !== 'default-cover.jpg') {
            return asset('storage/covers/' . $this->cover_photo);
        }
        return asset('images/default-cover.jpg');
    }

    public function getPrimaryRoleAttribute()
    {
        return $this->roles->first();
    }

    public function getPrimaryRoleBadgeAttribute(): string
    {
        $role = $this->primaryRole;
        if (!$role) return '<span class="badge badge-secondary">No Role</span>';
        $color = $role->color ?? '#6c757d';
        return '<span class="badge" style="background-color:' . $color . '">' . $role->name . '</span>';
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->state, $this->country, $this->postal_code]);
        return implode(', ', $parts);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    /**
     * Get all items visible to this user based on RBAC rules
     */
    public function getVisibleItems()
    {
        if ($this->isSuperAdmin()) {
            return Item::query();
        }

        if ($this->isAdmin()) {
            // Admin sees their own items + items created by users they created
            $myUserIds = $this->createdUsers()->pluck('id')->push($this->id);
            return Item::whereIn('created_by', $myUserIds);
        }

        // Regular user: sees own items + items where share_with_creator_admin = true
        // (items that the user chose to share with their admin)
        return Item::where(function ($q) {
            $q->where('created_by', $this->id)
              ->orWhere(function ($q2) {
                  $q2->where('share_with_creator_admin', true)
                     ->where('created_by', $this->id);
              });
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
}
