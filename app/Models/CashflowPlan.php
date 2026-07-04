<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashflowPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ledger_id', 'bank_account_id', 'title', 'expected_amount',
        'receipt_no', 'payer_name', 'reference_no', 'expected_date',
        'received_date', 'status', 'attachment_path', 'notes',
        'approved_by', 'approved_at', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'expected_date' => 'date',
            'received_date' => 'date',
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

    public function transaction()
    {
        return $this->morphOne(BankTransaction::class, 'transactionable');
    }
}
