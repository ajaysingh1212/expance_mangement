<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'ip_address', 'user_agent', 'properties',
    ];

    protected function casts(): array
    {
        return ['properties' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $description, $model = null, array $properties = []): void
    {
        $user = auth()->user();
        static::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'properties' => $properties,
        ]);
    }

    public function getActionBadgeAttribute(): string
    {
        $colors = [
            'created' => 'success', 'updated' => 'info',
            'deleted' => 'danger', 'login' => 'primary',
            'logout' => 'secondary', 'restored' => 'warning',
        ];
        $color = $colors[$this->action] ?? 'dark';
        return '<span class="badge badge-' . $color . '">' . ucfirst($this->action) . '</span>';
    }
}
