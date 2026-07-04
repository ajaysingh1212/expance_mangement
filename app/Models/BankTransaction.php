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
        'reconciliation_status',   // unreconciled | reconciled
        'reconciled_by',           // user_id who toggled
        'reconciled_at',           // timestamp of last toggle
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount'           => 'decimal:2',
            'balance_after'    => 'decimal:2',
            'reconciled_at'    => 'datetime',
        ];
    }

    // ── Relations ─────────────────────────────────────────────────

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    /** User who created / posted this transaction */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** User who last changed reconciliation status */
    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }
}
