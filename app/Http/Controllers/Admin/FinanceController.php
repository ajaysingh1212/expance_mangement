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

class FinanceController extends Controller
{
    public function ledgers()
    {
        $ledgers = Ledger::latest()->paginate(20);

        return view('admin.finance.ledgers', compact('ledgers'));
    }

    public function storeLedger(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:50', 'unique:ledgers,code'],
            'type' => ['required', Rule::in(['income', 'expense', 'salary', 'vendor', 'customer', 'bank', 'other'])],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:150'],
            'default_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $ledger = Ledger::create($data + ['created_by' => $request->user()->id]);
        ActivityLog::log('created', "Created ledger: {$ledger->name}", $ledger);

        return back()->with('success', 'Ledger created successfully.');
    }

    public function bankAccounts()
    {
        $bankAccounts = BankAccount::latest()->paginate(20);

        return view('admin.finance.bank-accounts', compact('bankAccounts'));
    }

    public function storeBankAccount(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:80'],
            'type' => ['required', Rule::in(['bank', 'cash', 'wallet'])],
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $account = BankAccount::create($data + [
            'current_balance' => $data['opening_balance'],
            'created_by' => $request->user()->id,
        ]);

        if ((float) $account->opening_balance > 0) {
            $this->recordBankTransaction(
                $account,
                null,
                'credit',
                (float) $account->opening_balance,
                now()->toDateString(),
                'Opening Balance',
                'OPENING',
                'Opening Balance',
                'Initial bank/cash balance',
                $request->user()->id
            );
        }

        ActivityLog::log('created', "Created bank account: {$account->name}", $account);

        return back()->with('success', 'Bank account created successfully.');
    }

    public function statement(Request $request)
    {
        $bankAccounts = BankAccount::where('status', 'active')->orderBy('name')->get();
        $selectedAccount = $request->integer('bank_account_id');
        $from = $request->date('from');
        $to = $request->date('to');

        $transactions = BankTransaction::with('bankAccount')
            ->when($selectedAccount, fn ($q) => $q->where('bank_account_id', $selectedAccount))
            ->when($from, fn ($q) => $q->whereDate('transaction_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('transaction_date', '<=', $to))
            ->latest('transaction_date')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $summary = [
            'credit' => (clone $transactions->getCollection())->where('direction', 'credit')->sum('amount'),
            'debit' => (clone $transactions->getCollection())->where('direction', 'debit')->sum('amount'),
        ];

        return view('admin.finance.statement', compact('bankAccounts', 'transactions', 'summary'));
    }

    public function cashflows()
    {
        $cashflows = CashflowPlan::with(['ledger', 'bankAccount'])->latest()->paginate(20);
        $ledgers = Ledger::whereIn('type', ['income', 'customer', 'other'])->active()->get();
        $bankAccounts = BankAccount::where('status', 'active')->get();

        return view('admin.finance.cashflows', compact('cashflows', 'ledgers', 'bankAccounts'));
    }

    public function storeCashflow(Request $request)
    {
        $data = $request->validate([
            'ledger_id' => ['nullable', 'exists:ledgers,id'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'title' => ['required', 'string', 'max:180'],
            'payer_name' => ['nullable', 'string', 'max:150'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'expected_amount' => ['required', 'numeric', 'min:1'],
            'expected_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['draft', 'submitted'])],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $data['attachment_path'] = $this->storeAttachment($request);
        unset($data['attachment']);

        $cashflow = CashflowPlan::create($data + [
            'receipt_no' => $this->nextDocumentNumber('RCPT'),
            'created_by' => $request->user()->id,
        ]);
        ActivityLog::log('created', "Created cashflow plan: {$cashflow->title}", $cashflow);
        $this->notifyFinanceApprovers(
            'Cashflow approval needed',
            "{$cashflow->title} expected inflow needs approval.",
            route('admin.dashboard'),
            'success',
            'fas fa-arrow-trend-up'
        );

        return back()->with('success', 'Cashflow plan saved.');
    }

    public function approveCashflow(Request $request, CashflowPlan $cashflow)
    {
        abort_unless($request->user()->can('finance.approve'), 403);

        if (in_array($cashflow->status, ['approved', 'received'], true)) {
            return back()->with('success', 'Cashflow is already approved.');
        }

        $cashflow->update([
            'status' => 'approved',
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
            'reference_no' => ['nullable', 'string', 'max:100'],
        ]);

        if ($cashflow->status !== 'approved') {
            return back()->with('error', 'Only approved cashflow can be received.');
        }

        DB::transaction(function () use ($request, $cashflow, $data) {
            $account = BankAccount::lockForUpdate()->findOrFail($cashflow->bank_account_id);
            $account->increment('current_balance', $cashflow->expected_amount);
            $account->refresh();

            $cashflow->update([
                'status' => 'received',
                'received_date' => $data['received_date'],
                'reference_no' => $data['reference_no'] ?? $cashflow->reference_no,
            ]);

            $this->recordBankTransaction(
                $account,
                $cashflow,
                'credit',
                (float) $cashflow->expected_amount,
                $data['received_date'],
                $cashflow->payer_name ?: $cashflow->ledger?->name,
                $data['reference_no'] ?? $cashflow->reference_no,
                'Cash Inflow',
                $cashflow->title,
                $request->user()->id
            );

            ActivityLog::log('received', "Received cashflow: {$cashflow->title}", $cashflow);
        });

        return back()->with('success', 'Cash received and bank statement updated.');
    }

    public function expenses()
    {
        $expenses = ExpensePlan::with(['ledger', 'bankAccount', 'payments'])->latest()->paginate(20);
        $ledgers = Ledger::whereIn('type', ['expense', 'salary', 'vendor', 'other'])->where('status', 'active')->get();
        $bankAccounts = BankAccount::where('status', 'active')->get();

        return view('admin.finance.expenses', compact('expenses', 'ledgers', 'bankAccounts'));
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'ledger_id' => ['required', 'exists:ledgers,id'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'title' => ['required', 'string', 'max:180'],
            'category' => ['nullable', 'string', 'max:100'],
            'vendor_name' => ['nullable', 'string', 'max:150'],
            'vendor_gstin' => ['nullable', 'string', 'max:30'],
            'payment_terms' => ['nullable', 'string', 'max:120'],
            'planned_amount' => ['required', 'numeric', 'min:1'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'expense_month' => ['nullable', 'date_format:Y-m'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['draft', 'submitted'])],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $data['attachment_path'] = $this->storeAttachment($request);
        $data['tax_amount'] = $data['tax_amount'] ?? 0;
        $data['discount_amount'] = $data['discount_amount'] ?? 0;
        $data['net_amount'] = ((float) $data['planned_amount'] + (float) $data['tax_amount']) - (float) $data['discount_amount'];
        unset($data['attachment']);

        $expense = ExpensePlan::create($data + [
            'invoice_no' => $this->nextDocumentNumber('INV'),
            'created_by' => $request->user()->id,
        ]);
        ActivityLog::log('created', "Created expense plan: {$expense->title}", $expense);
        $this->notifyFinanceApprovers(
            'Expense approval needed',
            "{$expense->title} for Rs " . number_format((float) $expense->net_amount, 2) . ' needs approval.',
            route('admin.dashboard'),
            'warning',
            'fas fa-receipt'
        );

        return back()->with('success', 'Expense plan saved.');
    }

    public function invoice(ExpensePlan $expense)
    {
        $expense->load(['ledger', 'bankAccount', 'payments.bankAccount']);

        return view('admin.finance.invoice', compact('expense'));
    }

    public function approveExpense(Request $request, ExpensePlan $expense)
    {
        abort_unless($request->user()->can('finance.approve'), 403);

        if (! in_array($expense->status, ['submitted', 'draft', 'deferred'], true)) {
            return back()->with('success', 'Expense plan is already reviewed.');
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);
        ActivityLog::log('approved', "Approved expense plan: {$expense->title}", $expense);

        return back()->with('success', 'Expense approved. Pay full or partial amount when ready.');
    }

    public function deferExpense(ExpensePlan $expense)
    {
        $expense->update(['status' => 'deferred']);

        return back()->with('success', 'Expense moved to future planning.');
    }

    public function storePayment(Request $request, ExpensePlan $expense)
    {
        $data = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'amount' => ['required', 'numeric', 'min:1', 'max:' . $expense->remaining_amount],
            'payment_date' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['submitted'])],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:1500'],
        ]);

        if (! in_array($expense->status, ['approved', 'partial'], true)) {
            return back()->with('error', 'Approve this expense before recording payment.');
        }

        $data['attachment_path'] = $this->storeAttachment($request);
        unset($data['attachment']);

        $payment = $expense->payments()->create($data + ['created_by' => $request->user()->id]);
        ActivityLog::log('created', "Submitted payment for: {$expense->title}", $payment);

        return back()->with('success', 'Payment submitted for approval.');
    }

    public function approvePayment(Request $request, ExpensePayment $payment)
    {
        abort_unless($request->user()->can('finance.approve'), 403);

        if ($payment->status === 'approved') {
            return back()->with('success', 'Payment is already posted.');
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
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            $expense = ExpensePlan::lockForUpdate()->findOrFail($payment->expense_plan_id);
            $expense->increment('paid_amount', $payment->amount);
            $expense->refresh();
            $expense->update(['status' => $expense->remaining_amount <= 0 ? 'paid' : 'partial']);

            $this->recordBankTransaction(
                $account,
                $payment,
                'debit',
                (float) $payment->amount,
                $payment->payment_date,
                $expense->vendor_name ?: $expense->ledger?->name,
                $payment->reference_no,
                $expense->category ?: $expense->ledger?->type,
                $expense->title,
                $request->user()->id
            );

            ActivityLog::log('approved', "Approved payment for: {$expense->title}", $payment);
        });

        return back()->with('success', 'Payment approved and bank balance reduced.');
    }

    private function storeAttachment(Request $request): ?string
    {
        if (! $request->hasFile('attachment')) {
            return null;
        }

        return $request->file('attachment')->store('finance/attachments', 'public');
    }

    private function recordBankTransaction(
        BankAccount $account,
        mixed $source,
        string $direction,
        float $amount,
        string $date,
        ?string $party,
        ?string $reference,
        ?string $category,
        ?string $description,
        ?int $userId
    ): BankTransaction {
        return BankTransaction::create([
            'bank_account_id' => $account->id,
            'transactionable_type' => $source ? get_class($source) : null,
            'transactionable_id' => $source?->id,
            'transaction_no' => $this->nextDocumentNumber('TXN'),
            'transaction_date' => $date,
            'direction' => $direction,
            'amount' => $amount,
            'balance_after' => $account->current_balance,
            'party_name' => $party,
            'reference_no' => $reference,
            'category' => $category,
            'description' => $description,
            'created_by' => $userId,
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
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'icon' => $icon,
                'link' => $link,
            ]);
        });
    }
}
