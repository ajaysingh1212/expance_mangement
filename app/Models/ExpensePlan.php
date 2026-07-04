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
        'ledger_id', 'bank_account_id', 'title', 'invoice_no', 'category',
        'vendor_name', 'vendor_gstin', 'payment_terms', 'planned_amount',
        'tax_amount', 'discount_amount', 'net_amount', 'paid_amount',
        'due_date', 'expense_month', 'priority', 'status',
        'attachment_path', 'notes', 'approved_by', 'approved_at', 'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'planned_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'expense_month' => 'date',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getRemainingAmountAttribute(): float
    {
        $payable = (float) ($this->net_amount ?: $this->planned_amount);

        return max(0, $payable - (float) $this->paid_amount);
    }
}
