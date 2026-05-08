<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'category', 'price', 'status',
        'image', 'created_by', 'share_with_creator_admin',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'share_with_creator_admin' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/items/' . $this->image);
        }
        return asset('images/default-item.png');
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = ['active' => 'success', 'inactive' => 'danger', 'draft' => 'warning'];
        $color = $colors[$this->status] ?? 'secondary';
        return '<span class="badge badge-' . $color . '">' . ucfirst($this->status) . '</span>';
    }

    public function scopeForUser($query, User $user)
    {
        if ($user->isSuperAdmin()) return $query;

        if ($user->isAdmin()) {
            $myUserIds = $user->createdUsers()->pluck('id')->push($user->id);
            return $query->whereIn('created_by', $myUserIds);
        }

        // Regular user: only their own
        return $query->where('created_by', $user->id);
    }
}
