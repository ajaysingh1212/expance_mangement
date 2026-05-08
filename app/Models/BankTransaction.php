<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id',
        'transactionable_type',
        'transactionable_id',
        'transaction_no',
        'transaction_date',
        'direction',
        'amount',
        'balance_after',
        'party_name',
        'reference_no',
        'category',
        'description',
        'reconciliation_status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
