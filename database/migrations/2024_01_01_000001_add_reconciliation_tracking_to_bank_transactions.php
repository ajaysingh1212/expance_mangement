<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add reconciliation tracking columns to bank_transactions.
     *
     * Also adds reconciliation_status if it doesn't already exist
     * (safe to run even if the column was added before via a different migration).
     */
    public function up(): void
    {
        if (!Schema::hasTable('bank_transactions')) {
            return;
        }

        Schema::table('bank_transactions', function (Blueprint $table) {
            // Core reconciliation status — idempotent guard
            if (!Schema::hasColumn('bank_transactions', 'reconciliation_status')) {
                $table->string('reconciliation_status', 30)
                      ->default('unreconciled')
                      ->after('description')
                      ->index();
            }

            // Who toggled the reconciliation
            if (!Schema::hasColumn('bank_transactions', 'reconciled_by')) {
                $table->foreignId('reconciled_by')
                      ->nullable()
                      ->after('reconciliation_status')
                      ->constrained('users')
                      ->nullOnDelete();
            }

            // When it was last toggled
            if (!Schema::hasColumn('bank_transactions', 'reconciled_at')) {
                $table->timestamp('reconciled_at')->nullable()->after('reconciled_by');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('bank_transactions')) {
            return;
        }

        Schema::table('bank_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('bank_transactions', 'reconciled_by')) {
                $table->dropForeignIdFor(\App\Models\User::class, 'reconciled_by');
                $table->dropColumn('reconciled_by');
            }
            if (Schema::hasColumn('bank_transactions', 'reconciled_at')) {
                $table->dropColumn('reconciled_at');
            }
            if (Schema::hasColumn('bank_transactions', 'reconciliation_status')) {
                $table->dropColumn('reconciliation_status');
            }
        });
    }
};
