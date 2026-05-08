<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_plans', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->unique()->after('title');
            $table->string('vendor_name')->nullable()->after('category');
            $table->string('vendor_gstin')->nullable()->after('vendor_name');
            $table->string('payment_terms')->nullable()->after('vendor_gstin');
            $table->decimal('tax_amount', 14, 2)->default(0)->after('planned_amount');
            $table->decimal('discount_amount', 14, 2)->default(0)->after('tax_amount');
            $table->decimal('net_amount', 14, 2)->default(0)->after('discount_amount');
        });

        Schema::table('cashflow_plans', function (Blueprint $table) {
            $table->string('receipt_no')->nullable()->unique()->after('title');
            $table->date('received_date')->nullable()->after('expected_date');
            $table->string('payer_name')->nullable()->after('ledger_id');
            $table->string('reference_no')->nullable()->after('payer_name');
        });

        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->nullableMorphs('transactionable');
            $table->string('transaction_no')->unique();
            $table->date('transaction_date');
            $table->enum('direction', ['credit', 'debit']);
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->string('party_name')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->enum('reconciliation_status', ['unreconciled', 'reconciled', 'flagged'])->default('unreconciled');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');

        Schema::table('cashflow_plans', function (Blueprint $table) {
            $table->dropColumn(['receipt_no', 'received_date', 'payer_name', 'reference_no']);
        });

        Schema::table('expense_plans', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_no',
                'vendor_name',
                'vendor_gstin',
                'payment_terms',
                'tax_amount',
                'discount_amount',
                'net_amount',
            ]);
        });
    }
};
