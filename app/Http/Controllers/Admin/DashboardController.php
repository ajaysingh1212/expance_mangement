<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\CashflowPlan;
use App\Models\ExpensePayment;
use App\Models\ExpensePlan;
use App\Models\Item;
use App\Models\Ledger;
use App\Models\User;
use App\Models\UserNotification;
use Spatie\Permission\Models\Role;


class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->isSuperAdmin()) {
            $stats['total_users']   = User::count();
            $stats['total_items']   = Item::count();
            $stats['total_roles']   = Role::count();
            $stats['active_users']  = User::where('is_active', true)->count();
        } elseif ($user->isAdmin()) {
            $myUserIds = $user->createdUsers()->pluck('id')->push($user->id);
            $stats['total_users']   = User::whereIn('id', $myUserIds)->count();
            $stats['total_items']   = Item::whereIn('created_by', $myUserIds)->count();
            $stats['total_roles']   = Role::count();
            $stats['active_users']  = User::whereIn('id', $myUserIds)->where('is_active', true)->count();
        } else {
            $stats['total_items']  = Item::where('created_by', $user->id)->count();
            $stats['active_items'] = Item::where('created_by', $user->id)->where('status', 'active')->count();
            $stats['draft_items']  = Item::where('created_by', $user->id)->where('status', 'draft')->count();
        }

        // ── Recent Activity ───────────────────────────────────────────────
        $recentActivity = ActivityLog::with('user')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->take(8)
            ->get();

        $recentItems = Item::with('creator')
            ->forUser($user)
            ->latest()
            ->take(5)
            ->get();

        // ── Finance core data ─────────────────────────────────────────────
        $bankAccounts  = BankAccount::where('status', 'active')->orderBy('name')->get();
        $ledgers       = Ledger::where('status', 'active')->orderBy('name')->get();
        $expenseLedgers = $ledgers->whereIn('type', ['expense', 'salary', 'vendor', 'other']);
        $incomeLedgers  = $ledgers->whereIn('type', ['income', 'customer', 'other']);
        $chartFrom = request()->date('chart_from');
        $chartTo = request()->date('chart_to');
        $chartStatus = request('chart_status');

        // ── Expense plans (priority-sorted, active statuses) ──────────────
        $expensePlans = ExpensePlan::with(['ledger', 'bankAccount', 'creator.roles'])
            ->when(request()->filled('dash_role'), fn($q) => $q->whereHas('creator.roles', fn($r) => $r->where('name', request('dash_role'))))
            ->when(request()->filled('dash_user_id'), fn($q) => $q->where('created_by', request()->integer('dash_user_id')))
            ->when(request()->filled('dash_from'), fn($q) => $q->whereDate('expense_month', '>=', request()->date('dash_from')))
            ->when(request()->filled('dash_to'), fn($q) => $q->whereDate('expense_month', '<=', request()->date('dash_to')))
            ->whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->take(15)
            ->get();

        // ── Salary ledger plans grouped per ledger (carry-forward view) ───
        $salaryPlans = ExpensePlan::with('ledger')
            ->whereHas('ledger', fn($q) => $q->where('type', 'salary'))
            ->whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
            ->orderBy('expense_month')
            ->get()
            ->groupBy(fn($e) => $e->ledger?->name ?? 'Unknown');

        // ── Payments pending approval ─────────────────────────────────────
        $recentPayments = ExpensePayment::with(['expensePlan.ledger', 'bankAccount'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(4, ['*'], 'payments_page')
            ->withQueryString();

        // ── Cashflow plans ────────────────────────────────────────────────
        $cashflowPlans = CashflowPlan::with(['ledger', 'bankAccount'])
            ->whereIn('status', ['submitted', 'draft', 'approved'])
            ->orderBy('expected_date')
            ->paginate(4, ['*'], 'cashflows_page')
            ->withQueryString();

        // ── Finance KPIs ──────────────────────────────────────────────────
        $financeStats = [
            'bank_balance'    => BankAccount::where('status', 'active')->sum('current_balance'),
            'planned_income'  => CashflowPlan::whereIn('status', ['draft', 'submitted', 'approved'])->sum('expected_amount'),
            'planned_expense' => ExpensePlan::whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
                ->selectRaw('COALESCE(SUM(CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END), 0) as total')
                ->value('total') ?? 0,
            'outstanding'     => ExpensePlan::whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
                ->selectRaw('COALESCE(SUM((CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) - paid_amount), 0) as total')
                ->value('total') ?? 0,
            'salary_due'      => ExpensePlan::whereHas('ledger', fn($q) => $q->where('type', 'salary'))
                ->whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
                ->selectRaw('COALESCE(SUM((CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) - paid_amount), 0) as total')
                ->value('total') ?? 0,
            'unreconciled_count' => BankTransaction::where('reconciliation_status', 'unreconciled')->count(),
        ];

        // ── Monthly expense chart (last 6 months) ────────────────────────
        $monthlyExpense = ExpensePlan::selectRaw(
                "DATE_FORMAT(expense_month, '%Y-%m') as month_key, SUM(CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) as total"
            )
            ->whereNotNull('expense_month')
            ->when($chartFrom, fn($q) => $q->whereDate('expense_month', '>=', $chartFrom))
            ->when($chartTo, fn($q) => $q->whereDate('expense_month', '<=', $chartTo))
            ->when($chartStatus, fn($q) => $q->where('status', $chartStatus))
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->take(6)
            ->pluck('total', 'month_key');

        // ── Monthly cashflow chart ────────────────────────────────────────
        $monthlyCashflow = CashflowPlan::selectRaw(
                "DATE_FORMAT(expected_date, '%Y-%m') as month_key, SUM(expected_amount) as total"
            )
            ->whereNotNull('expected_date')
            ->when($chartFrom, fn($q) => $q->whereDate('expected_date', '>=', $chartFrom))
            ->when($chartTo, fn($q) => $q->whereDate('expected_date', '<=', $chartTo))
            ->when($chartStatus, fn($q) => $q->where('status', $chartStatus))
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->take(6)
            ->pluck('total', 'month_key');

        // ── Expense by status (pie chart) ─────────────────────────────────
        $expenseByStatus = ExpensePlan::selectRaw('status, COUNT(*) as total')
            ->when($chartFrom, fn($q) => $q->whereDate('expense_month', '>=', $chartFrom))
            ->when($chartTo, fn($q) => $q->whereDate('expense_month', '<=', $chartTo))
            ->when($chartStatus, fn($q) => $q->where('status', $chartStatus))
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── Expenses the current bank balance can cover ───────────────────
        $affordableExpenses = ExpensePlan::with('ledger')
            ->whereIn('status', ['approved', 'partial'])
            ->whereRaw(
                '((CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) - paid_amount) <= ?',
                [max(0, $financeStats['bank_balance'])]
            )
            ->orderBy('due_date')
            ->take(6)
            ->get();

        // ── Recent bank transactions ──────────────────────────────────────
        $recentTransactions = BankTransaction::with('bankAccount')
            ->latest('transaction_date')
            ->latest()
            ->paginate(4, ['*'], 'transactions_page')
            ->withQueryString();

        // ── Cashflows approved but not yet received ───────────────────────
        $awaitingReceipts = CashflowPlan::with(['ledger', 'bankAccount'])
            ->where('status', 'approved')
            ->orderBy('expected_date')
            ->paginate(3, ['*'], 'receipts_page')
            ->withQueryString();

        $users = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.dashboard.index', compact(
            'stats',
            'recentActivity',
            'recentItems',
            'bankAccounts',
            'ledgers',
            'expenseLedgers',
            'incomeLedgers',
            'expensePlans',
            'salaryPlans',
            'recentPayments',
            'cashflowPlans',
            'financeStats',
            'monthlyExpense',
            'monthlyCashflow',
            'expenseByStatus',
            'affordableExpenses',
            'recentTransactions',
            'awaitingReceipts',
            'users',
            'roles'
        ));
    }
}
