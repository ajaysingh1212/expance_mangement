<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpensePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_plan_id', 'bank_account_id', 'amount', 'payment_date',
        'reference_no', 'status', 'attachment_path', 'notes',
        'approved_by', 'approved_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function expensePlan(): BelongsTo
    {
        return $this->belongsTo(ExpensePlan::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function transaction()
    {
        return $this->morphOne(BankTransaction::class, 'transactionable');
    }
}
