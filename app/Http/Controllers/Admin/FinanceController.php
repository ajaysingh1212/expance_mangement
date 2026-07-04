<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\CashflowPlan;
use App\Models\ExpensePayment;
use App\Models\ExpensePlan;
use App\Models\Ledger;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role as PermissionRole;

class FinanceController extends Controller
{
    // ════════════════════════════════════════════════════════════════════
    //  LEDGERS
    // ════════════════════════════════════════════════════════════════════

    public function ledgers()
    {
        $ledgers = Ledger::latest()->paginate(25);
        return view('admin.finance.ledgers', compact('ledgers'));
    }

    public function showLedger(Ledger $ledger)
    {
        $ledger->load(['expensePlans.payments.bankAccount', 'cashflowPlans.bankAccount', 'creator']);

        $cashflowIds = $ledger->cashflowPlans()->withTrashed()->pluck('id');
        $paymentIds = ExpensePayment::whereHas('expensePlan', fn($q) => $q->withTrashed()->where('ledger_id', $ledger->id))->pluck('id');

        $transactions = BankTransaction::with(['bankAccount', 'creator'])
            ->where(function ($query) use ($cashflowIds, $paymentIds) {
                $query->where(function ($q) use ($cashflowIds) {
                    $q->where('transactionable_type', CashflowPlan::class)
                        ->whereIn('transactionable_id', $cashflowIds);
                })->orWhere(function ($q) use ($paymentIds) {
                    $q->where('transactionable_type', ExpensePayment::class)
                        ->whereIn('transactionable_id', $paymentIds);
                });
            })
            ->latest('transaction_date')
            ->latest()
            ->paginate(30);

        return view('admin.finance.entity-show', [
            'title' => 'Ledger Statement',
            'heading' => $ledger->name,
            'subheading' => trim(($ledger->code ?: 'No code') . ' - ' . ucfirst($ledger->type)),
            'backRoute' => route('admin.finance.ledgers.index'),
            'summary' => [
                'Default Amount' => $ledger->default_amount,
                'Expense Planned' => $ledger->expensePlans->sum('net_amount'),
                'Cash In Planned' => $ledger->cashflowPlans->sum('expected_amount'),
                'Posted Transactions' => $transactions->total(),
            ],
            'details' => [
                'Status' => ucfirst($ledger->status),
                'Contact' => $ledger->phone ?: $ledger->email ?: '-',
                'Description' => $ledger->description ?: '-',
                'Created By' => $ledger->creator?->name ?: '-',
            ],
            'transactions' => $transactions,
            'plans' => $ledger->expensePlans->map(fn($row) => ['type' => 'Expense', 'row' => $row])
                ->merge($ledger->cashflowPlans->map(fn($row) => ['type' => 'Cash In', 'row' => $row])),
        ]);
    }

    public function storeLedger(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'code'           => ['nullable', 'string', 'max:50', 'unique:ledgers,code'],
            'type'           => ['required', Rule::in(['income','expense','salary','vendor','customer','bank','other'])],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'phone'          => ['nullable', 'string', 'max:40'],
            'email'          => ['nullable', 'email', 'max:150'],
            'default_amount' => ['nullable', 'numeric', 'min:0'],
            'status'         => ['required', Rule::in(['active','inactive'])],
            'description'    => ['nullable', 'string', 'max:1000'],
        ]);

        $ledger = Ledger::create($data + ['created_by' => $request->user()->id]);
        ActivityLog::log('created', "Created ledger: {$ledger->name}", $ledger);

        return back()->with('success', "Ledger '{$ledger->name}' created successfully.");
    }

    public function updateLedger(Request $request, Ledger $ledger)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'code'           => ['nullable', 'string', 'max:50', Rule::unique('ledgers', 'code')->ignore($ledger->id)],
            'type'           => ['required', Rule::in(['income','expense','salary','vendor','customer','bank','other'])],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'phone'          => ['nullable', 'string', 'max:40'],
            'email'          => ['nullable', 'email', 'max:150'],
            'default_amount' => ['nullable', 'numeric', 'min:0'],
            'status'         => ['required', Rule::in(['active','inactive'])],
            'description'    => ['nullable', 'string', 'max:1000'],
        ]);

        $ledger->update($data);
        ActivityLog::log('updated', "Updated ledger: {$ledger->name}", $ledger);

        return back()->with('success', "Ledger '{$ledger->name}' updated successfully.");
    }

    public function destroyLedger(Request $request, Ledger $ledger)
    {
        $data = $request->validate([
            'transaction_action' => ['required', Rule::in(['keep', 'delete_revert'])],
        ]);

        DB::transaction(function () use ($ledger, $data) {
            $ledger->load(['cashflowPlans.transaction', 'expensePlans.payments.transaction']);

            if ($data['transaction_action'] === 'delete_revert') {
                foreach ($ledger->cashflowPlans as $cashflow) {
                    $this->deleteCashflowWithTransactions($cashflow, true);
                }
                foreach ($ledger->expensePlans as $expense) {
                    $this->deleteExpenseWithTransactions($expense, true);
                }
            }

            ActivityLog::log('deleted', "Deleted ledger: {$ledger->name}", $ledger);
            $ledger->delete();
        });

        return redirect()->route('admin.finance.ledgers.index')->with('success', 'Ledger deleted successfully.');
    }

    // ════════════════════════════════════════════════════════════════════
    //  BANK ACCOUNTS
    // ════════════════════════════════════════════════════════════════════

    public function bankAccounts()
    {
        $bankAccounts = BankAccount::latest()->paginate(20);
        return view('admin.finance.bank-accounts', compact('bankAccounts'));
    }

    public function showBankAccount(BankAccount $bankAccount)
    {
        $bankAccount->load(['creator', 'editor']);
        $transactions = $bankAccount->transactions()
            ->with(['creator'])
            ->latest('transaction_date')
            ->latest()
            ->paginate(30);

        return view('admin.finance.entity-show', [
            'title' => 'Bank Statement',
            'heading' => $bankAccount->name,
            'subheading' => ($bankAccount->bank_name ?: ucfirst($bankAccount->type)) . ' - ' . ($bankAccount->account_number ?: 'No account number'),
            'backRoute' => route('admin.finance.bank-accounts.index'),
            'summary' => [
                'Current Balance' => $bankAccount->current_balance,
                'Opening Balance' => $bankAccount->opening_balance,
                'Total Credit' => $transactions->getCollection()->where('direction', 'credit')->sum('amount'),
                'Total Debit' => $transactions->getCollection()->where('direction', 'debit')->sum('amount'),
            ],
            'details' => [
                'Type' => ucfirst($bankAccount->type),
                'Status' => ucfirst($bankAccount->status),
                'Opening Date' => $bankAccount->opening_balance_date?->format('d M Y') ?: '-',
                'Notes' => $bankAccount->notes ?: '-',
            ],
            'transactions' => $transactions,
            'plans' => collect(),
        ]);
    }

    public function storeBankAccount(Request $request)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:150'],
            'bank_name'       => ['nullable', 'string', 'max:150'],
            'account_number'  => ['nullable', 'string', 'max:80'],
            'type'            => ['required', Rule::in(['bank','cash','wallet'])],
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'opening_balance_date' => ['nullable', 'date'],
            'status'          => ['required', Rule::in(['active','inactive'])],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        $account = BankAccount::create($data + [
            'current_balance' => $data['opening_balance'],
            'created_by'      => $request->user()->id,
        ]);

        if ((float) $account->opening_balance > 0) {
            $this->recordBankTransaction(
                $account, null, 'credit',
                (float) $account->opening_balance,
                $data['opening_balance_date'] ?? now()->toDateString(),
                'Opening Balance', 'OPENING', 'Opening Balance',
                'Initial bank/cash balance', $request->user()->id
            );
        }

        ActivityLog::log('created', "Created bank account: {$account->name}", $account);
        return back()->with('success', "Bank account '{$account->name}' created successfully.");
    }

    public function updateBankAccount(Request $request, BankAccount $bankAccount)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:150'],
            'bank_name'       => ['nullable', 'string', 'max:150'],
            'account_number'  => ['nullable', 'string', 'max:80'],
            'type'            => ['required', Rule::in(['bank','cash','wallet'])],
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'opening_balance_date' => ['nullable', 'date'],
            'current_balance' => ['required', 'numeric', 'min:0'],
            'status'          => ['required', Rule::in(['active','inactive'])],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        $bankAccount->update($data + ['updated_by' => $request->user()->id]);
        ActivityLog::log('updated', "Updated bank account: {$bankAccount->name}", $bankAccount);

        return back()->with('success', "Bank account '{$bankAccount->name}' updated successfully.");
    }

    public function destroyBankAccount(Request $request, BankAccount $bankAccount)
    {
        $data = $request->validate([
            'transaction_action' => ['required', Rule::in(['keep', 'delete_revert'])],
        ]);

        DB::transaction(function () use ($bankAccount, $data) {
            if ($data['transaction_action'] === 'delete_revert') {
                $bankAccount->transactions()->latest()->get()->each(fn($txn) => $this->reverseAndDeleteTransaction($txn));
            }

            ActivityLog::log('deleted', "Deleted bank account: {$bankAccount->name}", $bankAccount);
            $bankAccount->delete();
        });

        return redirect()->route('admin.finance.bank-accounts.index')->with('success', 'Bank account deleted successfully.');
    }

    // ════════════════════════════════════════════════════════════════════
    //  BANK STATEMENT
    // ════════════════════════════════════════════════════════════════════

    public function statement(Request $request)
    {
        $bankAccounts    = BankAccount::where('status', 'active')->orderBy('name')->get();
        $selectedAccount = $request->integer('bank_account_id');
        $from            = $request->date('from');
        $to              = $request->date('to');
        $direction       = $request->input('direction'); // 'credit' | 'debit' | null

        $transactions = BankTransaction::with(['bankAccount', 'creator', 'reconciledBy'])
            ->when($selectedAccount, fn($q) => $q->where('bank_account_id', $selectedAccount))
            ->when($from,      fn($q) => $q->whereDate('transaction_date', '>=', $from))
            ->when($to,        fn($q) => $q->whereDate('transaction_date', '<=', $to))
            ->when($direction, fn($q) => $q->where('direction', $direction))
            ->latest('transaction_date')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        // Page-level summary (on current page only)
        $summary = [
            'credit' => $transactions->getCollection()->where('direction', 'credit')->sum('amount'),
            'debit'  => $transactions->getCollection()->where('direction', 'debit')->sum('amount'),
        ];

        return view('admin.finance.statement', compact('bankAccounts', 'transactions', 'summary'));
    }


    /**
     * Manual bank entry — for bank charges, corrections, etc.
     */
    public function storeManualBankEntry(Request $request)
    {
        abort_unless($request->user()->can('finance.approve'), 403);

        $data = $request->validate([
            'bank_account_id'  => ['required', 'exists:bank_accounts,id'],
            'direction'        => ['required', Rule::in(['credit', 'debit'])],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'party_name'       => ['nullable', 'string', 'max:150'],
            'reference_no'     => ['nullable', 'string', 'max:100'],
            'category'         => ['nullable', 'string', 'max:100'],
            'description'      => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $data) {
            $account = BankAccount::lockForUpdate()->findOrFail($data['bank_account_id']);

            if ($data['direction'] === 'debit') {
                if ((float) $account->current_balance < (float) $data['amount']) {
                    throw ValidationException::withMessages([
                        'amount' => 'Insufficient balance. Current balance: Rs ' .
                                    number_format($account->current_balance, 2),
                    ]);
                }
                $account->decrement('current_balance', $data['amount']);
            } else {
                $account->increment('current_balance', $data['amount']);
            }
            $account->refresh();

            $txn = $this->recordBankTransaction(
                $account,
                null,
                $data['direction'],
                (float) $data['amount'],
                $data['transaction_date'],
                $data['party_name'] ?? null,
                $data['reference_no'] ?? null,
                $data['category'] ?? 'manual',
                $data['description'],
                $request->user()->id
            );

            ActivityLog::log(
                'manual_entry',
                "Manual {$data['direction']} Rs {$data['amount']} on {$account->name}: {$data['description']}",
                $txn
            );
        });

        return back()->with('success', 'Manual entry posted and bank balance updated.');
    }

    /**
     * Toggle reconciliation status of a transaction.
     */
    public function updateReconciliation(Request $request, BankTransaction $transaction)
    {
        abort_unless($request->user()->can('finance.approve'), 403);

        $new = $transaction->reconciliation_status === 'reconciled'
            ? 'unreconciled'
            : 'reconciled';

        $transaction->update([
            'reconciliation_status' => $new,
            'reconciled_by'         => $request->user()->id,
            'reconciled_at'         => now(),
        ]);

        ActivityLog::log(
            'reconciled',
            "Marked TXN {$transaction->transaction_no} as {$new} on {$transaction->bankAccount?->name}",
            $transaction
        );

        return back()->with('success', "Transaction marked as {$new}.");
    }

    // ════════════════════════════════════════════════════════════════════
    //  CASHFLOWS
    // ════════════════════════════════════════════════════════════════════

    public function cashflows(Request $request)
    {
        $cashflows    = CashflowPlan::with(['ledger', 'bankAccount', 'creator', 'editor'])
            ->when($request->filled('ledger_id'), fn($q) => $q->where('ledger_id', $request->integer('ledger_id')))
            ->when($request->filled('bank_account_id'), fn($q) => $q->where('bank_account_id', $request->integer('bank_account_id')))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('from'), fn($q) => $q->whereDate('expected_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn($q) => $q->whereDate('expected_date', '<=', $request->date('to')))
            ->latest()
            ->paginate(20)
            ->withQueryString();
        $ledgers      = Ledger::active()->orderBy('name')->get();
        $bankAccounts = BankAccount::where('status', 'active')->get();

        return view('admin.finance.cashflows', compact('cashflows', 'ledgers', 'bankAccounts'));
    }

    public function storeCashflow(Request $request)
    {
        $data = $request->validate([
            'ledger_id'       => ['nullable', 'exists:ledgers,id'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'title'           => ['required', 'string', 'max:180'],
            'payer_name'      => ['nullable', 'string', 'max:150'],
            'reference_no'    => ['nullable', 'string', 'max:100'],
            'expected_amount' => ['required', 'numeric', 'min:1'],
            'expected_date'   => ['required', 'date'],
            'status'          => ['required', Rule::in(['draft','submitted'])],
            'attachment'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:4096'],
            'notes'           => ['nullable', 'string', 'max:1500'],
        ]);

        $data['attachment_path'] = $this->storeAttachment($request);
        unset($data['attachment']);

        $cashflow = CashflowPlan::create($data + [
            'receipt_no' => $this->nextDocumentNumber('RCPT'),
            'created_by' => $request->user()->id,
        ]);

        ActivityLog::log('created', "Created cashflow plan: {$cashflow->title}", $cashflow);
        $this->notifyFinanceApprovers(
            'Cashflow Approval Needed',
            "{$cashflow->title} — expected Rs " . number_format((float) $cashflow->expected_amount, 2) . ' inflow awaits approval.',
            route('admin.dashboard'), 'success', 'fas fa-arrow-trend-up'
        );

        return back()->with('success', 'Cashflow plan saved.');
    }

    public function showCashflow(CashflowPlan $cashflow)
    {
        $cashflow->load(['ledger', 'bankAccount', 'creator', 'editor', 'approver', 'transaction.bankAccount']);
        $transactions = BankTransaction::with(['bankAccount', 'creator'])
            ->where('transactionable_type', CashflowPlan::class)
            ->where('transactionable_id', $cashflow->id)
            ->latest('transaction_date')
            ->paginate(30);

        return view('admin.finance.entity-show', [
            'title' => 'Cashflow Statement',
            'heading' => $cashflow->title,
            'subheading' => ($cashflow->receipt_no ?: 'No receipt') . ' - ' . ucfirst($cashflow->status),
            'backRoute' => route('admin.finance.cashflows.index'),
            'summary' => [
                'Expected Amount' => $cashflow->expected_amount,
                'Posted Credit' => $transactions->getCollection()->where('direction', 'credit')->sum('amount'),
                'Expected Date' => $cashflow->expected_date?->format('d M Y') ?: '-',
                'Received Date' => $cashflow->received_date?->format('d M Y') ?: '-',
            ],
            'details' => [
                'Source Ledger' => $cashflow->ledger?->name ?: 'Direct',
                'Payer' => $cashflow->payer_name ?: '-',
                'Bank Account' => $cashflow->bankAccount?->name ?: '-',
                'Reference' => $cashflow->reference_no ?: '-',
                'Notes' => $cashflow->notes ?: '-',
            ],
            'transactions' => $transactions,
            'plans' => collect([['type' => 'Cash In', 'row' => $cashflow]]),
        ]);
    }

    public function updateCashflow(Request $request, CashflowPlan $cashflow)
    {
        $data = $request->validate([
            'ledger_id'       => ['nullable', 'exists:ledgers,id'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'title'           => ['required', 'string', 'max:180'],
            'payer_name'      => ['nullable', 'string', 'max:150'],
            'reference_no'    => ['nullable', 'string', 'max:100'],
            'expected_amount' => ['required', 'numeric', 'min:1'],
            'expected_date'   => ['required', 'date'],
            'status'          => ['required', Rule::in(['draft','submitted','approved','rejected','cancelled'])],
            'notes'           => ['nullable', 'string', 'max:1500'],
        ]);

        if ($cashflow->status === 'received') {
            return back()->with('error', 'Received cashflow cannot be edited.');
        }

        $cashflow->update($data + ['updated_by' => $request->user()->id]);
        ActivityLog::log('updated', "Updated cashflow plan: {$cashflow->title}", $cashflow);

        return back()->with('success', 'Cashflow plan updated.');
    }

    public function destroyCashflow(Request $request, CashflowPlan $cashflow)
    {
        $data = $request->validate([
            'transaction_action' => ['required', Rule::in(['keep', 'delete_revert'])],
        ]);

        DB::transaction(function () use ($cashflow, $data) {
            $this->deleteCashflowWithTransactions($cashflow, $data['transaction_action'] === 'delete_revert');
            ActivityLog::log('deleted', "Deleted cashflow plan: {$cashflow->title}", $cashflow);
        });

        return redirect()->route('admin.finance.cashflows.index')->with('success', 'Cashflow plan deleted successfully.');
    }

    public function approveCashflow(Request $request, CashflowPlan $cashflow)
    {
        abort_unless($request->user()->can('finance.approve'), 403);

        if (in_array($cashflow->status, ['approved','received'], true)) {
            return back()->with('info', 'Cashflow is already approved.');
        }

        $cashflow->update([
            'status'      => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);
        ActivityLog::log('approved', "Approved cashflow: {$cashflow->title}", $cashflow);

        return back()->with('success', 'Cashflow approved. Confirm receipt when money arrives.');
    }

    public function receiveCashflow(Request $request, CashflowPlan $cashflow)
    {
        abort_unless($request->user()->can('finance.approve'), 403);

        $data = $request->validate([
            'received_date' => ['required', 'date'],
            'reference_no'  => ['nullable', 'string', 'max:100'],
        ]);

        if ($cashflow->status !== 'approved') {
            return back()->with('error', 'Only approved cashflow can be received.');
        }

        DB::transaction(function () use ($request, $cashflow, $data) {
            $account = BankAccount::lockForUpdate()->findOrFail($cashflow->bank_account_id);
            $account->increment('current_balance', $cashflow->expected_amount);
            $account->refresh();

            $cashflow->update([
                'status'        => 'received',
                'received_date' => $data['received_date'],
                'reference_no'  => $data['reference_no'] ?? $cashflow->reference_no,
            ]);

            $this->recordBankTransaction(
                $account, $cashflow, 'credit',
                (float) $cashflow->expected_amount,
                $data['received_date'],
                $cashflow->payer_name ?: $cashflow->ledger?->name,
                $data['reference_no'] ?? $cashflow->reference_no,
                'Cash Inflow', $cashflow->title,
                $request->user()->id
            );

            ActivityLog::log('received', "Received cashflow: {$cashflow->title}", $cashflow);
        });

        return back()->with('success', 'Cash received and bank balance updated.');
    }

    // ════════════════════════════════════════════════════════════════════
    //  EXPENSES
    // ════════════════════════════════════════════════════════════════════

    public function expenses(Request $request)
    {
        $expenses     = ExpensePlan::with(['ledger', 'bankAccount', 'payments', 'creator', 'editor'])
            ->when($request->filled('role'), function ($q) use ($request) {
                $role = $request->input('role');
                $q->whereHas('creator.roles', fn($r) => $r->where('name', $role));
            })
            ->when($request->filled('user_id'), fn($q) => $q->where('created_by', $request->integer('user_id')))
            ->when($request->filled('ledger_id'), fn($q) => $q->where('ledger_id', $request->integer('ledger_id')))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('from'), fn($q) => $q->whereDate('expense_month', '>=', $request->date('from')))
            ->when($request->filled('to'), fn($q) => $q->whereDate('expense_month', '<=', $request->date('to')))
            ->latest()
            ->paginate(20)
            ->withQueryString();
        $ledgers      = Ledger::whereIn('type', ['expense','salary','vendor','other'])->where('status', 'active')->get();
        $bankAccounts = BankAccount::where('status', 'active')->get();
        $users        = User::orderBy('name')->get();
        $roles        = PermissionRole::orderBy('name')->pluck('name');

        return view('admin.finance.expenses', compact('expenses', 'ledgers', 'bankAccounts', 'users', 'roles'));
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'ledger_id'       => ['required', 'exists:ledgers,id'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'title'           => ['required', 'string', 'max:180'],
            'vendor_name'     => ['nullable', 'string', 'max:150'],
            'planned_amount'  => ['required', 'numeric', 'min:1'],
            'tax_amount'      => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date'        => ['nullable', 'date'],
            'expense_month'   => ['nullable', 'date'],
            'priority'        => ['required', Rule::in(['low','normal','high','urgent'])],
            'status'          => ['required', Rule::in(['draft','submitted'])],
            'attachment'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:4096'],
            'notes'           => ['nullable', 'string', 'max:1500'],
        ]);

        $data['attachment_path'] = $this->storeAttachment($request);
        $data['tax_amount']      = $data['tax_amount'] ?? 0;
        $data['discount_amount'] = $data['discount_amount'] ?? 0;
        $data['net_amount']      = ((float) $data['planned_amount'] + (float) $data['tax_amount']) - (float) $data['discount_amount'];
        unset($data['attachment']);

        $expense = ExpensePlan::create($data + [
            'invoice_no' => $this->nextDocumentNumber('INV'),
            'created_by' => $request->user()->id,
        ]);

        ActivityLog::log('created', "Created expense plan: {$expense->title}", $expense);
        $this->notifyFinanceApprovers(
            'Expense Approval Needed',
            "{$expense->title} — Rs " . number_format((float) $expense->net_amount, 2) . ' needs approval.',
            route('admin.dashboard'), 'warning', 'fas fa-receipt'
        );

        return back()->with('success', 'Expense plan saved.');
    }

    public function updateExpense(Request $request, ExpensePlan $expense)
    {
        $data = $request->validate([
            'ledger_id'       => ['required', 'exists:ledgers,id'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'title'           => ['required', 'string', 'max:180'],
            'vendor_name'     => ['nullable', 'string', 'max:150'],
            'planned_amount'  => ['required', 'numeric', 'min:1'],
            'tax_amount'      => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date'        => ['nullable', 'date'],
            'expense_month'   => ['nullable', 'date'],
            'priority'        => ['required', Rule::in(['low','normal','high','urgent'])],
            'status'          => ['required', Rule::in(['draft','submitted','approved','partial','deferred','rejected','cancelled'])],
            'notes'           => ['nullable', 'string', 'max:1500'],
        ]);

        if (in_array($expense->status, ['paid'], true)) {
            return back()->with('error', 'Paid expense cannot be edited.');
        }

        $data['tax_amount']      = $data['tax_amount'] ?? 0;
        $data['discount_amount'] = $data['discount_amount'] ?? 0;
        $data['net_amount']      = ((float) $data['planned_amount'] + (float) $data['tax_amount']) - (float) $data['discount_amount'];

        $expense->update($data + ['updated_by' => $request->user()->id]);
        ActivityLog::log('updated', "Updated expense plan: {$expense->title}", $expense);

        return back()->with('success', 'Expense plan updated.');
    }

    public function invoice(ExpensePlan $expense)
    {
        $expense->load(['ledger', 'bankAccount', 'payments.bankAccount', 'approver']);
        return view('admin.finance.invoice', compact('expense'));
    }

    public function showExpense(ExpensePlan $expense)
    {
        $expense->load(['ledger', 'bankAccount', 'payments.bankAccount', 'payments.transaction.bankAccount', 'creator', 'editor', 'approver']);
        $paymentIds = $expense->payments()->withTrashed()->pluck('id');
        $transactions = BankTransaction::with(['bankAccount', 'creator'])
            ->where('transactionable_type', ExpensePayment::class)
            ->whereIn('transactionable_id', $paymentIds)
            ->latest('transaction_date')
            ->paginate(30);

        return view('admin.finance.entity-show', [
            'title' => 'Expense Statement',
            'heading' => $expense->title,
            'subheading' => ($expense->invoice_no ?: 'No invoice') . ' - ' . ucfirst($expense->status),
            'backRoute' => route('admin.finance.expenses.index'),
            'summary' => [
                'Net Payable' => $expense->net_amount ?: $expense->planned_amount,
                'Paid Amount' => $expense->paid_amount,
                'Balance' => $expense->remaining_amount,
                'Payment Count' => $expense->payments->count(),
            ],
            'details' => [
                'Ledger' => $expense->ledger?->name ?: '-',
                'Vendor' => $expense->vendor_name ?: '-',
                'Preferred Bank' => $expense->bankAccount?->name ?: '-',
                'Due Date' => $expense->due_date?->format('d M Y') ?: '-',
                'Notes' => $expense->notes ?: '-',
            ],
            'transactions' => $transactions,
            'plans' => collect([['type' => 'Expense', 'row' => $expense]]),
        ]);
    }

    public function approveExpense(Request $request, ExpensePlan $expense)
    {
        abort_unless($request->user()->can('finance.approve'), 403);

        if (!in_array($expense->status, ['submitted','draft','deferred'], true)) {
            return back()->with('info', 'Expense plan is already reviewed.');
        }

        $expense->update([
            'status'      => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);
        ActivityLog::log('approved', "Approved expense plan: {$expense->title}", $expense);

        return back()->with('success', 'Expense approved. Record payment when ready.');
    }

    public function deferExpense(ExpensePlan $expense)
    {
        $expense->update(['status' => 'deferred']);
        return back()->with('success', 'Expense deferred to future planning.');
    }

    public function rejectExpense(Request $request, ExpensePlan $expense)
    {
        abort_unless($request->user()->can('finance.approve'), 403);
        $expense->update(['status' => 'rejected']);
        ActivityLog::log('rejected', "Rejected expense plan: {$expense->title}", $expense);
        return back()->with('success', 'Expense rejected.');
    }

    public function destroyExpense(Request $request, ExpensePlan $expense)
    {
        $data = $request->validate([
            'transaction_action' => ['required', Rule::in(['keep', 'delete_revert'])],
        ]);

        DB::transaction(function () use ($expense, $data) {
            $this->deleteExpenseWithTransactions($expense, $data['transaction_action'] === 'delete_revert');
            ActivityLog::log('deleted', "Deleted expense plan: {$expense->title}", $expense);
        });

        return redirect()->route('admin.finance.expenses.index')->with('success', 'Expense plan deleted successfully.');
    }

    // ════════════════════════════════════════════════════════════════════
    //  PAYMENTS
    // ════════════════════════════════════════════════════════════════════

    public function storePayment(Request $request, ExpensePlan $expense)
    {
        $data = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'amount'          => ['required', 'numeric', 'min:1', 'max:' . $expense->remaining_amount],
            'payment_date'    => ['required', 'date'],
            'reference_no'    => ['nullable', 'string', 'max:100'],
            'status'          => ['nullable', Rule::in(['submitted'])],
            'attachment'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:4096'],
            'notes'           => ['nullable', 'string', 'max:1500'],
        ]);

        if (!in_array($expense->status, ['approved','partial'], true)) {
            return back()->with('error', 'Approve this expense before recording payment.');
        }

        $data['attachment_path'] = $this->storeAttachment($request);
        unset($data['attachment']);
        unset($data['status']);

        DB::transaction(function () use ($request, $expense, $data) {
            $expense = ExpensePlan::lockForUpdate()->findOrFail($expense->id);
            $account = BankAccount::lockForUpdate()->findOrFail($data['bank_account_id']);

            if ((float) $account->current_balance < (float) $data['amount']) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient bank balance for this payment.',
                ]);
            }

            $account->decrement('current_balance', $data['amount']);
            $account->refresh();

            $payment = $expense->payments()->create($data + [
                'status'      => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
                'created_by'  => $request->user()->id,
            ]);

            $expense->increment('paid_amount', $payment->amount);
            $expense->refresh();
            $expense->update(['status' => $expense->remaining_amount <= 0 ? 'paid' : 'partial']);

            $this->recordBankTransaction(
                $account, $payment, 'debit',
                (float) $payment->amount,
                $payment->payment_date,
                $expense->vendor_name ?: $expense->ledger?->name,
                $payment->reference_no,
                $expense->ledger?->type,
                $expense->title,
                $request->user()->id
            );

            ActivityLog::log('paid', "Payment posted for: {$expense->title}", $payment);
        });

        return back()->with('success', 'Payment posted and bank balance updated.');
    }

    public function plansReport(Request $request)
    {
        $expenseQuery = ExpensePlan::with(['ledger', 'bankAccount', 'creator.roles', 'editor']);
        $cashflowQuery = CashflowPlan::with(['ledger', 'bankAccount', 'creator.roles', 'editor']);

        $type = $request->input('type', 'all');
        $role = $request->input('role');
        $userId = $request->integer('user_id');
        $ledgerId = $request->integer('ledger_id');
        $status = $request->input('status');
        $from = $request->date('from');
        $to = $request->date('to');

        $applyCommon = function ($query, string $dateColumn) use ($role, $userId, $ledgerId, $status, $from, $to) {
            return $query
                ->when($role, fn($q) => $q->whereHas('creator.roles', fn($r) => $r->where('name', $role)))
                ->when($userId, fn($q) => $q->where('created_by', $userId))
                ->when($ledgerId, fn($q) => $q->where('ledger_id', $ledgerId))
                ->when($status, fn($q) => $q->where('status', $status))
                ->when($from, fn($q) => $q->whereDate($dateColumn, '>=', $from))
                ->when($to, fn($q) => $q->whereDate($dateColumn, '<=', $to));
        };

        $rows = collect();
        if (in_array($type, ['all', 'expense'], true)) {
            $rows = $rows->merge($applyCommon($expenseQuery, 'expense_month')->latest()->get()->map(fn($row) => ['type' => 'Expense', 'row' => $row]));
        }
        if (in_array($type, ['all', 'cashflow'], true)) {
            $rows = $rows->merge($applyCommon($cashflowQuery, 'expected_date')->latest()->get()->map(fn($row) => ['type' => 'Cash In', 'row' => $row]));
        }

        $users = User::orderBy('name')->get();
        $roles = PermissionRole::orderBy('name')->pluck('name');
        $ledgers = Ledger::orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.finance.partials.plans-report-table', compact('rows'))->render(),
                'count' => $rows->count(),
            ]);
        }

        return view('admin.finance.plans-report', compact('rows', 'users', 'roles', 'ledgers'));
    }

    public function approvePayment(Request $request, ExpensePayment $payment)
    {
        abort_unless($request->user()->can('finance.approve'), 403);

        if ($payment->status === 'approved') {
            return back()->with('info', 'Payment is already posted.');
        }

        DB::transaction(function () use ($request, $payment) {
            $payment->load('expensePlan');
            $account = BankAccount::lockForUpdate()->findOrFail($payment->bank_account_id);

            if ((float) $account->current_balance < (float) $payment->amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient bank balance for this payment.',
                ]);
            }

            $account->decrement('current_balance', $payment->amount);
            $account->refresh();

            $payment->update([
                'status'      => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            $expense = ExpensePlan::lockForUpdate()->findOrFail($payment->expense_plan_id);
            $expense->increment('paid_amount', $payment->amount);
            $expense->refresh();
            $expense->update(['status' => $expense->remaining_amount <= 0 ? 'paid' : 'partial']);

            $this->recordBankTransaction(
                $account, $payment, 'debit',
                (float) $payment->amount,
                $payment->payment_date,
                $expense->vendor_name ?: $expense->ledger?->name,
                $payment->reference_no,
                $expense->ledger?->type,
                $expense->title,
                $request->user()->id
            );

            ActivityLog::log('approved', "Approved payment for: {$expense->title}", $payment);
        });

        return back()->with('success', 'Payment approved and bank balance updated.');
    }

    // ════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ════════════════════════════════════════════════════════════════════

    private function deleteCashflowWithTransactions(CashflowPlan $cashflow, bool $revertTransactions): void
    {
        $cashflow->loadMissing('transaction');

        if ($revertTransactions && $cashflow->transaction) {
            $this->reverseAndDeleteTransaction($cashflow->transaction);
        }

        $cashflow->delete();
    }

    private function deleteExpenseWithTransactions(ExpensePlan $expense, bool $revertTransactions): void
    {
        $expense->loadMissing('payments.transaction');

        if ($revertTransactions) {
            foreach ($expense->payments as $payment) {
                if ($payment->transaction) {
                    $this->reverseAndDeleteTransaction($payment->transaction);
                }
                $payment->delete();
            }
        }

        $expense->delete();
    }

    private function reverseAndDeleteTransaction(BankTransaction $transaction): void
    {
        $account = BankAccount::withTrashed()->lockForUpdate()->find($transaction->bank_account_id);

        if ($account) {
            if ($transaction->direction === 'credit') {
                $account->decrement('current_balance', $transaction->amount);
            } else {
                $account->increment('current_balance', $transaction->amount);
            }
        }

        $transaction->delete();
    }

    private function storeAttachment(Request $request): ?string
    {
        if (!$request->hasFile('attachment')) {
            return null;
        }
        return $request->file('attachment')->store('finance/attachments', 'public');
    }

    private function recordBankTransaction(
        BankAccount $account,
        mixed       $source,
        string      $direction,
        float       $amount,
        string      $date,
        ?string     $party,
        ?string     $reference,
        ?string     $category,
        ?string     $description,
        ?int        $userId
    ): BankTransaction {
        return BankTransaction::create([
            'bank_account_id'       => $account->id,
            'transactionable_type'  => $source ? get_class($source) : null,
            'transactionable_id'    => $source?->id,
            'transaction_no'        => $this->nextDocumentNumber('TXN'),
            'transaction_date'      => $date,
            'direction'             => $direction,
            'amount'                => $amount,
            'balance_after'         => $account->current_balance,
            'party_name'            => $party,
            'reference_no'          => $reference,
            'category'              => $category,
            'description'           => $description,
            'reconciliation_status' => 'unreconciled',
            'created_by'            => $userId,
        ]);
    }

    private function nextDocumentNumber(string $prefix): string
    {
        return $prefix . '-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function notifyFinanceApprovers(string $title, string $message, string $link, string $type, string $icon): void
    {
        User::permission('finance.approve')->get()->each(function (User $user) use ($title, $message, $link, $type, $icon) {
            UserNotification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'message' => $message,
                'type'    => $type,
                'icon'    => $icon,
                'link'    => $link,
            ]);
        });
    }
}
