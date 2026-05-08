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
            $stats['total_items']   = Item::where('created_by', $user->id)->count();
            $stats['active_items']  = Item::where('created_by', $user->id)->where('status', 'active')->count();
            $stats['draft_items']   = Item::where('created_by', $user->id)->where('status', 'draft')->count();
        }

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

        $bankAccounts = BankAccount::where('status', 'active')->latest()->get();
        $ledgers = Ledger::where('status', 'active')->orderBy('name')->get();
        $expenseLedgers = $ledgers->whereIn('type', ['expense', 'salary', 'vendor', 'other']);
        $incomeLedgers = $ledgers->whereIn('type', ['income', 'customer', 'other']);
        $expensePlans = ExpensePlan::with(['ledger', 'bankAccount'])
            ->whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->take(12)
            ->get();
        $pendingPayments = ExpensePayment::with(['expensePlan.ledger', 'bankAccount'])
            ->where('status', 'submitted')
            ->latest()
            ->take(8)
            ->get();
        $cashflowPlans = CashflowPlan::with(['ledger', 'bankAccount'])
            ->whereIn('status', ['submitted', 'draft', 'approved'])
            ->orderBy('expected_date')
            ->take(8)
            ->get();

        $financeStats = [
            'bank_balance' => BankAccount::sum('current_balance'),
            'planned_income' => CashflowPlan::whereIn('status', ['draft', 'submitted', 'approved'])->sum('expected_amount'),
            'planned_expense' => ExpensePlan::whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
                ->selectRaw('COALESCE(SUM(CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END), 0) as total')
                ->value('total') ?? 0,
            'outstanding' => ExpensePlan::whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
                ->selectRaw('COALESCE(SUM((CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) - paid_amount), 0) as total')
                ->value('total') ?? 0,
            'salary_due' => ExpensePlan::whereHas('ledger', fn ($q) => $q->where('type', 'salary'))
                ->whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
                ->selectRaw('COALESCE(SUM((CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) - paid_amount), 0) as total')
                ->value('total') ?? 0,
        ];

        $monthlyExpense = ExpensePlan::selectRaw('expense_month as month_key, SUM(CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) as total')
            ->whereNotNull('expense_month')
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->take(6)
            ->pluck('total', 'month_key');
        $expenseByStatus = ExpensePlan::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $affordableExpenses = ExpensePlan::with('ledger')
            ->whereIn('status', ['approved', 'partial'])
            ->whereRaw('((CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) - paid_amount) <= ?', [max(0, $financeStats['bank_balance'])])
            ->orderBy('due_date')
            ->take(5)
            ->get();
        $recentTransactions = BankTransaction::with('bankAccount')
            ->latest('transaction_date')
            ->latest()
            ->take(8)
            ->get();
        $awaitingReceipts = CashflowPlan::with(['ledger', 'bankAccount'])
            ->where('status', 'approved')
            ->orderBy('expected_date')
            ->take(8)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'recentActivity',
            'recentItems',
            'bankAccounts',
            'ledgers',
            'expenseLedgers',
            'incomeLedgers',
            'expensePlans',
            'pendingPayments',
            'cashflowPlans',
            'financeStats',
            'monthlyExpense',
            'expenseByStatus',
            'affordableExpenses',
            'recentTransactions',
            'awaitingReceipts'
        ));
    }
}
