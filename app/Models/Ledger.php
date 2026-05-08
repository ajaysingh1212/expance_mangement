<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ledger extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'code', 'type', 'contact_person', 'phone', 'email',
        'default_amount', 'status', 'description', 'created_by',
    ];

    protected function casts(): array
    {
        return ['default_amount' => 'decimal:2'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expensePlans(): HasMany
    {
        return $this->hasMany(ExpensePlan::class);
    }

    public function cashflowPlans(): HasMany
    {
        return $this->hasMany(CashflowPlan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
