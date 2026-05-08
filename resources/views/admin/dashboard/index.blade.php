@extends('admin.layouts.app')

@section('title', 'Finance Dashboard')
@section('page-title', 'Finance Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
@php
    $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2);
@endphp

<div class="page-header-card mb-4" style="background:#0f172a;">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <h1>Expense & Cashflow Command Center</h1>
            <p>Plan income, approve expenses, post salary payments, and keep bank balances matched from one place.</p>
        </div>
        <div class="col-lg-5 text-lg-right mt-3 mt-lg-0">
            @can('finance.ledgers.create')
            <button class="btn btn-light btn-sm mr-1 mb-1" data-toggle="modal" data-target="#ledgerModal"><i class="fas fa-book mr-1"></i> Ledger</button>
            @endcan
            @can('finance.bank.create')
            <button class="btn btn-light btn-sm mr-1 mb-1" data-toggle="modal" data-target="#bankModal"><i class="fas fa-building-columns mr-1"></i> Bank</button>
            @endcan
            @can('finance.cashflows.create')
            <button class="btn btn-success btn-sm mr-1 mb-1" data-toggle="modal" data-target="#cashflowModal"><i class="fas fa-arrow-trend-up mr-1"></i> Cash In</button>
            @endcan
            @can('finance.expenses.create')
            <button class="btn btn-warning btn-sm mb-1" data-toggle="modal" data-target="#expenseModal"><i class="fas fa-receipt mr-1"></i> Expense</button>
            @endcan
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="info-box"><span class="info-box-icon" style="background:#0f766e;color:#fff;"><i class="fas fa-wallet"></i></span><div class="info-box-content"><span class="info-box-text">Bank Balance</span><span class="info-box-number">{{ $money($financeStats['bank_balance'] ?? 0) }}</span></div></div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="info-box"><span class="info-box-icon" style="background:#2563eb;color:#fff;"><i class="fas fa-arrow-trend-up"></i></span><div class="info-box-content"><span class="info-box-text">Expected Inflow</span><span class="info-box-number">{{ $money($financeStats['planned_income'] ?? 0) }}</span></div></div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="info-box"><span class="info-box-icon" style="background:#ea580c;color:#fff;"><i class="fas fa-file-invoice"></i></span><div class="info-box-content"><span class="info-box-text">Planned Expense</span><span class="info-box-number">{{ $money($financeStats['planned_expense'] ?? 0) }}</span></div></div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="info-box"><span class="info-box-icon" style="background:#be123c;color:#fff;"><i class="fas fa-hourglass-half"></i></span><div class="info-box-content"><span class="info-box-text">Outstanding</span><span class="info-box-number">{{ $money($financeStats['outstanding'] ?? 0) }}</span><div style="font-size:0.75rem;color:#64748b;">Salary due: {{ $money($financeStats['salary_due'] ?? 0) }}</div></div></div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-calendar-check mr-2 text-primary"></i>Expense Planning & Carry Forward</h3>
                @can('finance.expenses.index')<a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-sm btn-outline-primary">Open</a>@endcan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Ledger</th><th>Month/Due</th><th>Planned</th><th>Paid</th><th>Balance</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @forelse($expensePlans as $expense)
                            <tr>
                                <td><strong>{{ $expense->title }}</strong><div class="text-muted small">{{ $expense->ledger?->name }} · {{ ucfirst($expense->ledger?->type ?? 'expense') }}</div></td>
                                <td>{{ $expense->expense_month ?: '-' }}<div class="text-muted small">{{ $expense->due_date?->format('d M Y') ?: 'No due date' }}</div></td>
                                <td>{{ $money($expense->planned_amount) }}</td>
                                <td>{{ $money($expense->paid_amount) }}</td>
                                <td><strong>{{ $money($expense->remaining_amount) }}</strong></td>
                                <td><span class="badge badge-{{ match($expense->status) {'paid'=>'success','partial'=>'warning','approved'=>'info','deferred'=>'secondary','rejected'=>'danger',default=>'light'} }}">{{ ucfirst($expense->status) }}</span></td>
                                <td class="text-right">
                                    @can('finance.approve')
                                    @if(in_array($expense->status, ['submitted', 'draft', 'deferred']))
                                    <form action="{{ route('admin.finance.expenses.approve', $expense) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button></form>
                                    @endif
                                    @endcan
                                    @can('finance.payments.create')
                                    @if(in_array($expense->status, ['approved', 'partial']) && $expense->remaining_amount > 0)
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paymentModal{{ $expense->id }}"><i class="fas fa-money-bill-wave"></i></button>
                                    @endif
                                    @endcan
                                    @can('finance.expenses.edit')
                                    @if(!in_array($expense->status, ['paid', 'deferred']))
                                    <form action="{{ route('admin.finance.expenses.defer', $expense) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-secondary"><i class="fas fa-clock"></i></button></form>
                                    @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No expense plans yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header"><h3><i class="fas fa-chart-pie mr-2 text-info"></i>Planning Charts</h3></div>
            <div class="card-body">
                <canvas id="statusChart" height="190"></canvas>
                <hr>
                <canvas id="monthlyChart" height="170"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header"><h3><i class="fas fa-building-columns mr-2 text-success"></i>Bank & Cash</h3></div>
            <div class="card-body">
                @forelse($bankAccounts as $account)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom:1px solid #eef2f7;">
                    <div><strong>{{ $account->name }}</strong><div class="text-muted small">{{ $account->bank_name ?: ucfirst($account->type) }}</div></div>
                    <span class="font-weight-bold">{{ $money($account->current_balance) }}</span>
                </div>
                @empty
                <div class="text-center text-muted py-4">Add a bank account to start posting approvals.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header"><h3><i class="fas fa-thumbs-up mr-2 text-primary"></i>Can Pay Now</h3></div>
            <div class="card-body">
                @forelse($affordableExpenses as $expense)
                <div class="mb-3 pb-3" style="border-bottom:1px solid #eef2f7;">
                    <strong>{{ $expense->title }}</strong>
                    <div class="d-flex justify-content-between text-muted small"><span>{{ $expense->ledger?->name }}</span><span>{{ $money($expense->remaining_amount) }}</span></div>
                </div>
                @empty
                <div class="text-center text-muted py-4">No approved expenses fit the current balance yet.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header"><h3><i class="fas fa-check-double mr-2 text-warning"></i>Approval Queue</h3></div>
            <div class="card-body">
                @forelse($pendingPayments as $payment)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom:1px solid #eef2f7;">
                    <div><strong>{{ $payment->expensePlan?->title }}</strong><div class="text-muted small">{{ $payment->bankAccount?->name }} · {{ $money($payment->amount) }}</div></div>
                    @can('finance.approve')
                    <form action="{{ route('admin.finance.payments.approve', $payment) }}" method="POST">@csrf<button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button></form>
                    @endcan
                </div>
                @empty
                <div class="text-center text-muted py-4">No payment approvals pending.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@include('admin.finance.partials.modals')

@foreach($expensePlans as $expense)
    @include('admin.finance.partials.payment-modal', ['expense' => $expense, 'bankAccounts' => $bankAccounts])
@endforeach
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const statusLabels = @json($expenseByStatus->keys()->values());
const statusValues = @json($expenseByStatus->values()->map(fn($v) => (int) $v)->values());
const monthlyLabels = @json($monthlyExpense->keys()->values());
const monthlyValues = @json($monthlyExpense->values()->map(fn($v) => (float) $v)->values());

new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: { labels: statusLabels, datasets: [{ data: statusValues, backgroundColor: ['#2563eb', '#f59e0b', '#16a34a', '#64748b', '#dc2626', '#0f766e'] }] },
    options: { plugins: { legend: { position: 'bottom' } } }
});
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: { labels: monthlyLabels, datasets: [{ label: 'Planned Expense', data: monthlyValues, borderColor: '#0f766e', backgroundColor: 'rgba(15,118,110,.12)', fill: true, tension: .35 }] },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
