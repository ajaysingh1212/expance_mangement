<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpensePlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ledger_id', 'bank_account_id', 'title', 'category', 'planned_amount',
        'paid_amount', 'due_date', 'expense_month', 'priority', 'status',
        'attachment_path', 'notes', 'approved_by', 'approved_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'planned_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ExpensePayment::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->planned_amount - (float) $this->paid_amount);
    }
}
