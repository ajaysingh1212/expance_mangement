@extends('admin.layouts.app')

@section('title', 'Finance Dashboard')
@section('page-title', 'Finance Command Center')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Finance Dashboard</li>
@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════════
   FINANCE DASHBOARD — Ultra-Professional Design
   ═══════════════════════════════════════════════════════════════ */
:root {
    --fc-bg:        #f1f5f9;
    --fc-card:      #ffffff;
    --fc-border:    #e2e8f0;
    --fc-text:      #0f172a;
    --fc-muted:     #64748b;
    --fc-primary:   #2563eb;
    --fc-success:   #059669;
    --fc-warning:   #d97706;
    --fc-danger:    #dc2626;
    --fc-info:      #0284c7;
    --fc-salary:    #7c3aed;
    --fc-radius:    12px;
    --fc-shadow:    0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.04);
    --fc-shadow-lg: 0 4px 24px rgba(0,0,0,.12);
}

body { background: var(--fc-bg) !important; }

/* ── Header Banner ── */
.fin-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #0f172a 100%);
    border-radius: var(--fc-radius);
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.fin-header::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(37,99,235,.35) 0%, transparent 70%);
    border-radius: 50%;
}
.fin-header::after {
    content: '';
    position: absolute;
    bottom: -60px; left: 30%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(5,150,105,.18) 0%, transparent 70%);
    border-radius: 50%;
}
.fin-header h1 { color: #fff; font-size: 1.6rem; font-weight: 700; margin: 0 0 4px; }
.fin-header p  { color: #94a3b8; font-size: .875rem; margin: 0; }
.fin-header .action-bar { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; }
.fin-header .action-bar .btn {
    font-size: .8rem; font-weight: 600; padding: 8px 16px;
    border-radius: 8px; letter-spacing: .02em; border: none;
    transition: all .2s ease;
}
.fin-header .action-bar .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.25); }
.btn-glass {
    background: rgba(255,255,255,.12) !important;
    color: #fff !important;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.2) !important;
}
.btn-glass:hover { background: rgba(255,255,255,.22) !important; }

/* ── KPI Cards ── */
.kpi-card {
    background: var(--fc-card);
    border-radius: var(--fc-radius);
    padding: 20px 22px;
    box-shadow: var(--fc-shadow);
    border: 1px solid var(--fc-border);
    position: relative;
    overflow: hidden;
    transition: box-shadow .2s;
}
.kpi-card:hover { box-shadow: var(--fc-shadow-lg); }
.dash-animated { animation: riseIn .45s ease both; }
.dash-animated::after {
    content: '';
    position: absolute;
    inset: auto 18px 14px auto;
    width: 56px;
    height: 56px;
    border: 1px solid rgba(15,23,42,.08);
    border-radius: 14px;
    transform: rotate(12deg);
}
@keyframes riseIn {
    from { opacity: 0; transform: translateY(14px); }
    to { opacity: 1; transform: translateY(0); }
}
.kpi-card .kpi-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; margin-bottom: 14px;
}
.kpi-card .kpi-label { font-size: .75rem; font-weight: 600; color: var(--fc-muted); text-transform: uppercase; letter-spacing: .06em; }
.kpi-card .kpi-value { font-size: 1.5rem; font-weight: 700; color: var(--fc-text); line-height: 1.2; margin: 4px 0 0; }
.kpi-card .kpi-sub   { font-size: .75rem; color: var(--fc-muted); margin-top: 4px; }
.kpi-card .kpi-bar   { position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 12px 0 0 12px; }

/* ── Section Cards ── */
.fc-card {
    background: var(--fc-card);
    border-radius: var(--fc-radius);
    box-shadow: var(--fc-shadow);
    border: 1px solid var(--fc-border);
    overflow: hidden;
}
.fc-card-header {
    padding: 16px 20px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid var(--fc-border);
    background: #fafbfc;
}
.fc-card-header h3 { font-size: .9rem; font-weight: 700; color: var(--fc-text); margin: 0; display: flex; align-items: center; gap: 8px; }
.fc-card-header .badge-count { background: var(--fc-primary); color: #fff; font-size: .7rem; padding: 2px 7px; border-radius: 99px; font-weight: 600; }
.fc-card-body { padding: 0; }
.fc-card-body.padded { padding: 16px 20px; }
.fc-card-footer { padding: 10px 20px; border-top: 1px solid var(--fc-border); background: #fafbfc; }
.compact-pager {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
}
.compact-pager .pager-btn {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    color: #0f172a;
    border: 1px solid var(--fc-border);
    text-decoration: none;
    transition: all .16s ease;
}
.compact-pager .pager-btn:not(.disabled):hover {
    background: #0f172a;
    color: #fff;
    transform: translateY(-1px);
}
.compact-pager .pager-btn.disabled {
    opacity: .4;
    pointer-events: none;
}
.compact-pager .pager-meta {
    font-size: .74rem;
    color: var(--fc-muted);
    font-weight: 700;
}

/* ── Data Table ── */
.fin-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
.fin-table thead th {
    padding: 10px 14px; background: #f8fafc; color: var(--fc-muted);
    font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    border-bottom: 1px solid var(--fc-border); white-space: nowrap;
}
.fin-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .15s; }
.fin-table tbody tr:hover { background: #f8fafc; }
.fin-table tbody td { padding: 11px 14px; vertical-align: middle; color: var(--fc-text); }
.fin-table .td-primary { font-weight: 600; }
.fin-table .td-sub     { font-size: .72rem; color: var(--fc-muted); margin-top: 2px; }
.fin-table .td-amount  { font-weight: 700; font-variant-numeric: tabular-nums; }

/* ── Status Badges ── */
.s-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .7rem; font-weight: 700; padding: 3px 9px;
    border-radius: 99px; letter-spacing: .03em; text-transform: uppercase;
}
.s-draft     { background: #f1f5f9; color: #475569; }
.s-submitted { background: #eff6ff; color: #1d4ed8; }
.s-approved  { background: #ecfdf5; color: #047857; }
.s-partial   { background: #fffbeb; color: #b45309; }
.s-paid      { background: #dcfce7; color: #15803d; }
.s-deferred  { background: #faf5ff; color: #7c3aed; }
.s-rejected  { background: #fef2f2; color: #dc2626; }
.s-received  { background: #dcfce7; color: #15803d; }
.s-unreconciled { background: #fffbeb; color: #b45309; }
.s-reconciled   { background: #ecfdf5; color: #047857; }
.process-bot {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 5px 9px;
    font-size: .72rem;
    font-weight: 700;
    white-space: nowrap;
}
.process-bot-wait { background:#eff6ff; color:#1d4ed8; }
.process-bot-ready { background:#fffbeb; color:#b45309; }
.process-bot-done { background:#ecfdf5; color:#047857; }

/* ── Priority Dots ── */
.p-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
.p-urgent { background: #dc2626; }
.p-high   { background: #d97706; }
.p-normal { background: #2563eb; }
.p-low    { background: #94a3b8; }

/* ── Action Buttons ── */
.act-btn {
    width: 30px; height: 30px; border-radius: 7px; border: none;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .8rem; cursor: pointer; transition: all .15s; text-decoration: none;
}
.act-btn:hover { transform: translateY(-1px); }
.act-approve { background: #dcfce7; color: #15803d; }
.act-approve:hover { background: #059669; color: #fff; }
.act-pay     { background: #dbeafe; color: #1d4ed8; }
.act-pay:hover { background: #2563eb; color: #fff; }
.act-invoice { background: #f1f5f9; color: #475569; }
.act-invoice:hover { background: #0f172a; color: #fff; }
.act-defer   { background: #faf5ff; color: #7c3aed; }
.act-defer:hover { background: #7c3aed; color: #fff; }
.act-reject  { background: #fef2f2; color: #dc2626; }
.act-reject:hover { background: #dc2626; color: #fff; }

/* ── Sidebar items ── */
.bank-item {
    display: flex; align-items: center; gap: 12px; padding: 13px 0;
    border-bottom: 1px solid #f1f5f9;
}
.bank-item:last-child { border-bottom: none; padding-bottom: 0; }
.bank-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .9rem; flex-shrink: 0; }
.bank-icon.bank-type-bank   { background: #dbeafe; color: #1d4ed8; }
.bank-icon.bank-type-cash   { background: #dcfce7; color: #059669; }
.bank-icon.bank-type-wallet { background: #f3e8ff; color: #7c3aed; }
.bank-name  { font-weight: 600; font-size: .84rem; color: var(--fc-text); }
.bank-sub   { font-size: .72rem; color: var(--fc-muted); }
.bank-bal   { font-weight: 700; font-size: .95rem; margin-left: auto; white-space: nowrap; }

/* ── Cashflow / transaction rows ── */
.cf-row {
    display: flex; align-items: flex-start; gap: 12px; padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}
.cf-row:last-child { border-bottom: none; padding-bottom: 0; }
.cf-dot { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .85rem; flex-shrink: 0; }
.cf-in  { background: #dcfce7; color: #059669; }
.cf-out { background: #fef2f2; color: #dc2626; }

/* ── Confirm Inflow form ── */
.receive-form { padding: 14px; background: #f8fafc; border-radius: 10px; margin-bottom: 12px; border: 1px solid var(--fc-border); }
.receive-form:last-child { margin-bottom: 0; }
.receive-form .rf-title { font-weight: 600; font-size: .85rem; color: var(--fc-text); }
.receive-form .rf-meta  { font-size: .73rem; color: var(--fc-muted); margin-bottom: 10px; }
.receive-form .rf-amount { font-weight: 700; color: var(--fc-success); }

/* ── Salary carry-forward table ── */
.salary-employee {
    padding: 12px 0; border-bottom: 1px solid #f1f5f9;
}
.salary-employee:last-child { border-bottom: none; }
.salary-name { font-weight: 600; font-size: .84rem; }
.salary-months { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
.salary-chip {
    display: inline-flex; align-items: center; gap: 4px;
    background: #faf5ff; color: #7c3aed; border-radius: 6px;
    padding: 3px 8px; font-size: .72rem; font-weight: 600;
}
.salary-chip.paid { background: #dcfce7; color: #059669; }

/* ── Charts ── */
.chart-wrap { position: relative; }
.chart-card-animated {
    position: relative;
    overflow: hidden;
    animation: chartLift .55s ease both;
}
.chart-card-animated::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        linear-gradient(120deg, transparent 0%, rgba(37,99,235,.05) 45%, transparent 70%);
    transform: translateX(-100%);
    animation: chartSheen 3.8s ease-in-out infinite;
    pointer-events: none;
}
.chart-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}
.chart-filter .form-control { min-width: 135px; }
@keyframes chartLift {
    from { opacity: 0; transform: translateY(16px) scale(.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes chartSheen {
    0%, 58% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* ── Modals ── */
.fin-modal .modal-content {
    border: none; border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
}
.fin-modal .modal-header {
    background: #0f172a; color: #fff; border-radius: 16px 16px 0 0;
    padding: 18px 24px; border: none;
}
.fin-modal .modal-header h5 { font-size: 1rem; font-weight: 700; margin: 0; }
.fin-modal .modal-header .close { color: #94a3b8; opacity: 1; font-size: 1.4rem; }
.fin-modal .modal-header .close:hover { color: #fff; }
.fin-modal .modal-body { padding: 24px; }
.fin-modal .modal-footer { padding: 16px 24px; background: #f8fafc; border-radius: 0 0 16px 16px; border-top: 1px solid var(--fc-border); }
.fin-modal .form-group label { font-size: .78rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px; }
.fin-modal .form-control {
    border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: .85rem;
    padding: 8px 12px; transition: border-color .2s, box-shadow .2s;
}
.fin-modal .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); outline: none; }
.fin-modal .section-divider {
    font-size: .7rem; font-weight: 700; color: var(--fc-muted);
    text-transform: uppercase; letter-spacing: .08em;
    margin: 8px 0 16px; display: flex; align-items: center; gap: 10px;
}
.fin-modal .section-divider::after { content: ''; flex: 1; height: 1px; background: var(--fc-border); }
.net-calc-box {
    background: #0f172a; color: #fff; border-radius: 10px; padding: 14px 16px;
    display: flex; justify-content: space-between; align-items: center; margin-top: 4px;
}
.net-calc-box .net-label { font-size: .75rem; color: #94a3b8; }
.net-calc-box .net-value { font-size: 1.25rem; font-weight: 800; }
.attachment-thumb { max-height: 80px; border-radius: 8px; border: 1px solid var(--fc-border); margin-top: 8px; }
.file-badge { display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; color: #475569; border-radius: 6px; padding: 4px 10px; font-size: .75rem; font-weight: 600; margin-top: 8px; }

/* ── Alert / Info box inside modal ── */
.balance-alert {
    background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px;
    padding: 10px 14px; font-size: .82rem; color: #1e40af; margin-bottom: 16px;
}
.balance-alert strong { font-weight: 800; }

/* ── Unreconciled badge pulse ── */
@keyframes pulse-badge {
    0%, 100% { opacity: 1; }
    50%       { opacity: .6; }
}
.pulse-badge { animation: pulse-badge 2s ease-in-out infinite; }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 40px 20px; color: var(--fc-muted); }
.empty-state i { font-size: 2rem; margin-bottom: 10px; display: block; opacity: .4; }
.empty-state p { font-size: .82rem; margin: 0; }

/* ── Responsive ── */
@media (max-width: 991px) {
    .fin-header h1 { font-size: 1.2rem; }
    .kpi-value { font-size: 1.2rem; }
}
</style>
@endpush

@section('content')
@php
    $money = fn($v) => 'Rs ' . number_format((float) $v, 2);
    $statusClass = fn($s) => match($s) {
        'paid'      => 's-paid',
        'partial'   => 's-partial',
        'approved'  => 's-approved',
        'deferred'  => 's-deferred',
        'rejected'  => 's-rejected',
        'submitted' => 's-submitted',
        'received'  => 's-received',
        'draft'     => 's-draft',
        default     => 's-draft',
    };
@endphp

{{-- ══════════════════════════════════════════════════════════════
     HEADER BANNER
══════════════════════════════════════════════════════════════ --}}
<div class="fin-header">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-3 mb-lg-0">
            <h1><i class="fas fa-chart-line mr-2" style="color:#38bdf8;"></i>Finance Command Center</h1>
            <p>Real-time expense management, cashflow planning, salary tracking &amp; bank reconciliation.</p>
        </div>
        <div class="col-lg-6">
            <div class="action-bar">
                @can('finance.ledgers.create')
                <button class="btn btn-glass" data-toggle="modal" data-target="#ledgerModal">
                    <i class="fas fa-book mr-1"></i> Ledger
                </button>
                @endcan
                @can('finance.bank.create')
                <button class="btn btn-glass" data-toggle="modal" data-target="#bankModal">
                    <i class="fas fa-building-columns mr-1"></i> Bank A/C
                </button>
                @endcan
                @can('finance.bank.index')
                <a href="{{ route('admin.finance.statement.index') }}" class="btn btn-glass">
                    <i class="fas fa-file-lines mr-1"></i> Statement
                </a>
                @endcan
                @can('finance.cashflows.create')
                <button class="btn" style="background:#059669;color:#fff;" data-toggle="modal" data-target="#cashflowModal">
                    <i class="fas fa-arrow-trend-up mr-1"></i> Cash In
                </button>
                @endcan
                @can('finance.expenses.create')
                <button class="btn" style="background:#d97706;color:#fff;" data-toggle="modal" data-target="#expenseModal">
                    <i class="fas fa-receipt mr-1"></i> Expense
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     KPI ROW
══════════════════════════════════════════════════════════════ --}}
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="kpi-card dash-animated">
            <div class="kpi-bar" style="background:#059669;"></div>
            <div class="kpi-icon" style="background:#dcfce7;color:#059669;"><i class="fas fa-wallet"></i></div>
            <div class="kpi-label">Bank Balance</div>
            <div class="kpi-value">{{ $money($financeStats['bank_balance'] ?? 0) }}</div>
            <div class="kpi-sub">{{ $bankAccounts->count() }} active account{{ $bankAccounts->count() != 1 ? 's' : '' }}</div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="kpi-card dash-animated" style="animation-delay:.08s;">
            <div class="kpi-bar" style="background:#0284c7;"></div>
            <div class="kpi-icon" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-arrow-trend-up"></i></div>
            <div class="kpi-label">Expected Inflow</div>
            <div class="kpi-value">{{ $money($financeStats['planned_income'] ?? 0) }}</div>
            <div class="kpi-sub">Planned &amp; approved cashflows</div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12 mb-3">
        <div class="kpi-card dash-animated" style="animation-delay:.16s;">
            <div class="kpi-bar" style="background:#d97706;"></div>
            <div class="kpi-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-file-invoice"></i></div>
            <div class="kpi-label">Planned Expense</div>
            <div class="kpi-value">{{ $money($financeStats['planned_expense'] ?? 0) }}</div>
            <div class="kpi-sub">All active expense plans</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MAIN ROW: EXPENSE PLANS + SIDEBAR
══════════════════════════════════════════════════════════════ --}}
<div class="row mb-4">

    {{-- Expense Plans Table --}}
    <div class="col-xl-8 mb-4 mb-xl-0">
        <div class="fc-card h-100">
            <div class="fc-card-header">
                <h3>
                    <span style="width:8px;height:8px;background:#d97706;border-radius:50%;display:inline-block;"></span>
                    Expense &amp; Salary Plans
                    <span class="badge-count">{{ $expensePlans->count() }}</span>
                </h3>
                @can('finance.expenses.index')
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <a href="{{ route('admin.finance.plans.report') }}" class="btn btn-sm" style="font-size:.75rem;background:#0f172a;color:#fff;border-radius:7px;font-weight:600;">
                        Advanced Filters <i class="fas fa-filter ml-1"></i>
                    </a>
                    <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-sm" style="font-size:.75rem;background:#f1f5f9;color:#374151;border-radius:7px;font-weight:600;">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endcan
            </div>
            <div class="fc-card-body">
                <form method="GET" class="p-3" style="border-bottom:1px solid var(--fc-border);background:#f8fafc;">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <select name="dash_role" class="form-control form-control-sm">
                                <option value="">All Roles</option>
                                @foreach($roles ?? [] as $role)
                                <option value="{{ $role }}" @selected(request('dash_role') === $role)>{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="dash_user_id" class="form-control form-control-sm">
                                <option value="">All Users</option>
                                @foreach($users ?? [] as $u)
                                <option value="{{ $u->id }}" @selected((string) request('dash_user_id') === (string) $u->id)>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2"><input type="date" name="dash_from" value="{{ request('dash_from') }}" class="form-control form-control-sm"></div>
                        <div class="col-md-2 mb-2"><input type="date" name="dash_to" value="{{ request('dash_to') }}" class="form-control form-control-sm"></div>
                        <div class="col-md-2 mb-2" style="display:flex;gap:6px;">
                            <button class="btn btn-sm btn-primary" style="flex:1;"><i class="fas fa-search"></i></button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light"><i class="fas fa-rotate-left"></i></a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="fin-table">
                        <thead>
                            <tr>
                                <th>Expense</th>
                                <th>User / Role</th>
                                <th>Expense Date / Due</th>
                                <th>Next Step</th>
                                <th>Net</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($expensePlans as $expense)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span class="p-dot p-{{ $expense->priority }}"></span>
                                    <div>
                                        <div class="td-primary">{{ Str::limit($expense->title, 32) }}</div>
                                        <div class="td-sub">{{ $expense->ledger?->name }} · {{ ucfirst($expense->ledger?->type ?? '') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:.82rem;font-weight:600;">{{ $expense->creator?->name ?? '-' }}</div>
                                <div class="td-sub">{{ $expense->creator?->roles?->pluck('name')->implode(', ') ?: 'No role' }}</div>
                            </td>
                            <td>
                                <div style="font-size:.8rem;font-weight:600;">{{ $expense->expense_month?->format('d M Y') ?: '---' }}</div>
                                <div class="td-sub">{{ $expense->due_date?->format('d M Y') ?: 'No due date' }}</div>
                            </td>
                            <td>@include('admin.finance.partials.process-bot', ['status' => $expense->status, 'type' => 'expense'])</td>
                            <td class="td-amount">{{ $money($expense->net_amount ?: $expense->planned_amount) }}</td>
                            <td class="td-amount" style="color:var(--fc-success);">{{ $money($expense->paid_amount) }}</td>
                            <td class="td-amount" style="color:{{ $expense->remaining_amount > 0 ? 'var(--fc-danger)' : 'var(--fc-success)' }};">
                                {{ $money($expense->remaining_amount) }}
                            </td>
                            <td><span class="s-badge {{ $statusClass($expense->status) }}">{{ ucfirst($expense->status) }}</span></td>
                            <td>
                                <div style="display:flex;gap:4px;justify-content:flex-end;">
                                    @can('finance.approve')
                                    @if(in_array($expense->status, ['submitted','draft','deferred']))
                                    <form action="{{ route('admin.finance.expenses.approve', $expense) }}" method="POST" class="d-inline">@csrf
                                        <button type="submit" class="act-btn act-approve" title="Approve"><i class="fas fa-check"></i></button>
                                    </form>
                                    @endif
                                    @endcan

                                    @can('finance.payments.create')
                                    @if(in_array($expense->status, ['approved','partial']) && $expense->remaining_amount > 0)
                                    <button class="act-btn act-pay" data-toggle="modal" data-target="#paymentModal{{ $expense->id }}" title="Pay"><i class="fas fa-money-bill-wave"></i></button>
                                    @endif
                                    @endcan

                                    @can('finance.expenses.show')
                                    <a href="{{ route('admin.finance.expenses.invoice', $expense) }}" target="_blank" class="act-btn act-invoice" title="Invoice"><i class="fas fa-file-invoice"></i></a>
                                    @endcan

                                    @can('finance.expenses.edit')
                                    @if(!in_array($expense->status, ['paid','deferred','rejected']))
                                    <form action="{{ route('admin.finance.expenses.defer', $expense) }}" method="POST" class="d-inline">@csrf
                                        <button type="submit" class="act-btn act-defer" title="Defer"><i class="fas fa-clock"></i></button>
                                    </form>
                                    @endif
                                    @endcan

                                    @can('finance.approve')
                                    @if(!in_array($expense->status, ['paid','rejected']))
                                    <form action="{{ route('admin.finance.expenses.reject', $expense) }}" method="POST" class="d-inline">@csrf
                                        <button type="submit" class="act-btn act-reject" title="Reject" onclick="return confirm('Reject this expense?')"><i class="fas fa-times"></i></button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9"><div class="empty-state"><i class="fas fa-receipt"></i><p>No active expense plans.</p></div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar: Banks + Can Pay --}}
    <div class="col-xl-4">
        {{-- Bank Accounts --}}
        <div class="fc-card mb-4">
            <div class="fc-card-header">
                <h3><i class="fas fa-building-columns" style="color:#059669;"></i> Bank &amp; Cash</h3>
                @can('finance.bank.create')
                <button class="act-btn act-approve" data-toggle="modal" data-target="#bankModal" title="Add Account" style="width:26px;height:26px;">
                    <i class="fas fa-plus"></i>
                </button>
                @endcan
            </div>
            <div class="fc-card-body padded">
                @forelse($bankAccounts as $account)
                <div class="bank-item">
                    <div class="bank-icon bank-type-{{ $account->type }}">
                        <i class="fas fa-{{ $account->type === 'bank' ? 'building-columns' : ($account->type === 'wallet' ? 'wallet' : 'money-bill') }}"></i>
                    </div>
                    <div>
                        <div class="bank-name">{{ $account->name }}</div>
                        <div class="bank-sub">{{ $account->bank_name ?: ucfirst($account->type) }}
                            @if($account->account_number) · ••{{ substr($account->account_number, -4) }}@endif
                        </div>
                    </div>
                    <div class="bank-bal" style="color:{{ $account->current_balance >= 0 ? 'var(--fc-success)' : 'var(--fc-danger)' }};">
                        {{ $money($account->current_balance) }}
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class="fas fa-building-columns"></i><p>No bank accounts added.</p></div>
                @endforelse
            </div>
            @if($bankAccounts->count() > 0)
            <div class="fc-card-footer" style="text-align:right;">
                <span style="font-size:.75rem;color:var(--fc-muted);">Total: </span>
                <strong style="color:var(--fc-success);">{{ $money($financeStats['bank_balance'] ?? 0) }}</strong>
            </div>
            @endif
        </div>

        {{-- Can Pay Now --}}
        <div class="fc-card">
            <div class="fc-card-header">
                <h3><i class="fas fa-thumbs-up" style="color:#2563eb;"></i> Can Pay Now</h3>
            </div>
            <div class="fc-card-body padded">
                @forelse($affordableExpenses as $expense)
                <div class="salary-employee">
                    <div class="salary-name">{{ $expense->title }}</div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                        <span style="font-size:.73rem;color:var(--fc-muted);">{{ $expense->ledger?->name }}</span>
                        <span style="font-weight:700;font-size:.85rem;color:var(--fc-success);">{{ $money($expense->remaining_amount) }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class="fas fa-circle-check"></i><p>No approved expenses fit current balance.</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     SALARY CARRY-FORWARD + CASHFLOW PLANS
══════════════════════════════════════════════════════════════ --}}
<div class="row mb-4">
    {{-- Salary Carry-Forward --}}
    <div class="col-xl-4 mb-4 mb-xl-0">
        <div class="fc-card h-100">
            <div class="fc-card-header">
                <h3><i class="fas fa-users" style="color:#7c3aed;"></i> Salary Carry-Forward</h3>
            </div>
            <div class="fc-card-body padded">
                @forelse($salaryPlans as $employee => $plans)
                <div class="salary-employee">
                    <div class="salary-name">{{ $employee }}</div>
                    <div style="font-size:.73rem;color:var(--fc-muted);margin-bottom:6px;">
                        Total outstanding: <strong style="color:#7c3aed;">{{ $money($plans->sum('remaining_amount')) }}</strong>
                    </div>
                    <div class="salary-months">
                        @foreach($plans as $plan)
                        <span class="salary-chip {{ $plan->status === 'paid' ? 'paid' : '' }}">
                            {{ $plan->expense_month ?: 'No month' }}
                            · {{ $money($plan->remaining_amount) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class="fas fa-users"></i><p>No salary carry-forward pending.</p></div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Cashflow Plans --}}
    <div class="col-xl-8">
        <div class="fc-card h-100">
            <div class="fc-card-header">
                <h3>
                    <span style="width:8px;height:8px;background:#0284c7;border-radius:50%;display:inline-block;"></span>
                    Cashflow Plans
                    <span class="badge-count">{{ $cashflowPlans->count() }}</span>
                </h3>
                @can('finance.cashflows.index')
                <a href="{{ route('admin.finance.cashflows.index') }}" class="btn btn-sm" style="font-size:.75rem;background:#f1f5f9;color:#374151;border-radius:7px;font-weight:600;">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
                @endcan
            </div>
            <div class="fc-card-body">
                <table class="fin-table">
                    <thead>
                        <tr>
                            <th>Title / Source</th>
                            <th>Bank Account</th>
                            <th>Expected</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($cashflowPlans as $cf)
                    <tr>
                        <td>
                            <div class="td-primary">{{ Str::limit($cf->title, 30) }}</div>
                            <div class="td-sub">{{ $cf->payer_name ?: $cf->ledger?->name ?: 'Direct' }}</div>
                        </td>
                        <td><div style="font-size:.82rem;">{{ $cf->bankAccount?->name }}</div></td>
                        <td class="td-amount" style="color:var(--fc-success);">{{ $money($cf->expected_amount) }}</td>
                        <td><div style="font-size:.8rem;">{{ $cf->expected_date?->format('d M Y') }}</div></td>
                        <td><span class="s-badge {{ $statusClass($cf->status) }}">{{ ucfirst($cf->status) }}</span></td>
                        <td>
                            <div style="display:flex;gap:4px;justify-content:flex-end;">
                                @can('finance.approve')
                                @if(!in_array($cf->status, ['received','approved']))
                                <form action="{{ route('admin.finance.cashflows.approve', $cf) }}" method="POST" class="d-inline">@csrf
                                    <button class="act-btn act-approve" title="Approve"><i class="fas fa-check"></i></button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6"><div class="empty-state"><i class="fas fa-arrow-trend-up"></i><p>No active cashflow plans.</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     BOTTOM ROW: CONFIRM INFLOW + APPROVAL QUEUE + TRANSACTIONS
══════════════════════════════════════════════════════════════ --}}
<div class="row mb-4">

    {{-- Confirm Inflow --}}
    <div class="col-xl-4 mb-4">
        <div class="fc-card h-100">
            <div class="fc-card-header">
                <h3><i class="fas fa-circle-check" style="color:#059669;"></i> Confirm Receipt</h3>
            </div>
            <div class="fc-card-body padded">
                @forelse($awaitingReceipts as $cf)
                <form action="{{ route('admin.finance.cashflows.receive', $cf) }}" method="POST" class="receive-form">
                    @csrf
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                        <div class="rf-title">{{ $cf->title }}</div>
                        <span class="rf-amount">{{ $money($cf->expected_amount) }}</span>
                    </div>
                    <div class="rf-meta">{{ $cf->bankAccount?->name }} · Expected {{ $cf->expected_date?->format('d M Y') }}</div>
                    <div style="display:flex;gap:8px;">
                        <input type="date" name="received_date" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required style="border-radius:7px;font-size:.78rem;flex:1;">
                        <input type="text" name="reference_no" class="form-control form-control-sm" placeholder="Ref #" style="border-radius:7px;font-size:.78rem;flex:1;">
                        @can('finance.approve')
                        <button type="submit" class="btn btn-sm" style="background:#059669;color:#fff;border-radius:7px;white-space:nowrap;font-size:.78rem;font-weight:600;">
                            <i class="fas fa-check"></i>
                        </button>
                        @endcan
                    </div>
                </form>
                @empty
                <div class="empty-state"><i class="fas fa-inbox"></i><p>No approved inflows awaiting confirmation.</p></div>
                @endforelse
                @include('admin.finance.partials.compact-pager', ['paginator' => $awaitingReceipts])
            </div>
        </div>
    </div>

    {{-- Approval Queue --}}
    <div class="col-xl-4 mb-4">
        <div class="fc-card h-100">
            <div class="fc-card-header">
                <h3><i class="fas fa-money-check-alt" style="color:#d97706;"></i> Recent Payments
                    @if($recentPayments->count() > 0)
                    <span class="badge-count">{{ $recentPayments->count() }}</span>
                    @endif
                </h3>
            </div>
            <div class="fc-card-body padded">
                @forelse($recentPayments as $payment)
                <div class="cf-row">
                    <div class="cf-dot cf-out"><i class="fas fa-money-bill-wave"></i></div>
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:.84rem;">{{ $payment->expensePlan?->title }}</div>
                        <div class="td-sub">{{ $payment->bankAccount?->name }} · {{ $payment->payment_date?->format('d M Y') }}</div>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                        <span style="font-weight:700;font-size:.9rem;color:var(--fc-danger);">{{ $money($payment->amount) }}</span>
                        <span class="s-badge s-paid">Done</span>
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class="fas fa-check-circle"></i><p>No payments posted yet.</p></div>
                @endforelse
                @include('admin.finance.partials.compact-pager', ['paginator' => $recentPayments])
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="col-xl-4 mb-4">
        <div class="fc-card h-100">
            <div class="fc-card-header">
                <h3><i class="fas fa-file-lines" style="color:#0284c7;"></i> Recent Transactions</h3>
                @can('finance.bank.index')
                <a href="{{ route('admin.finance.statement.index') }}" class="btn btn-sm" style="font-size:.75rem;background:#f1f5f9;color:#374151;border-radius:7px;font-weight:600;">Full</a>
                @endcan
            </div>
            <div class="fc-card-body padded">
                @forelse($recentTransactions as $txn)
                <div class="cf-row">
                    <div class="cf-dot {{ $txn->direction === 'credit' ? 'cf-in' : 'cf-out' }}">
                        <i class="fas fa-arrow-{{ $txn->direction === 'credit' ? 'down' : 'up' }}"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:.82rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $txn->party_name ?: $txn->description }}
                        </div>
                        <div class="td-sub">{{ $txn->bankAccount?->name }} · {{ $txn->transaction_date?->format('d M Y') }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-weight:700;font-size:.85rem;color:{{ $txn->direction === 'credit' ? 'var(--fc-success)' : 'var(--fc-danger)' }};">
                            {{ $txn->direction === 'credit' ? '+' : '-' }}{{ $money($txn->amount) }}
                        </div>
                        <div class="td-sub">Bal {{ $money($txn->balance_after) }}</div>
                    </div>
                </div>
                @empty
                <div class="empty-state"><i class="fas fa-file-lines"></i><p>No transactions posted yet.</p></div>
                @endforelse
                @include('admin.finance.partials.compact-pager', ['paginator' => $recentTransactions])
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     CHARTS ROW
══════════════════════════════════════════════════════════════ --}}
<form method="GET" class="fc-card mb-3 chart-card-animated">
    <div class="fc-card-header">
        <h3><i class="fas fa-sliders" style="color:#2563eb;"></i> Chart Filters</h3>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light" style="border-radius:7px;font-weight:600;">
            <i class="fas fa-rotate-left"></i>
        </a>
    </div>
    <div class="fc-card-body padded">
        <div class="chart-filter">
            <input type="date" name="chart_from" class="form-control form-control-sm" value="{{ request('chart_from') }}">
            <input type="date" name="chart_to" class="form-control form-control-sm" value="{{ request('chart_to') }}">
            <select name="chart_status" class="form-control form-control-sm">
                <option value="">All Status</option>
                @foreach(['draft','submitted','approved','partial','paid','received','deferred','rejected','cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('chart_status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary" style="border-radius:7px;font-weight:700;">
                <i class="fas fa-wand-magic-sparkles mr-1"></i> Apply
            </button>
        </div>
    </div>
</form>

<div class="row mb-4">
    <div class="col-xl-4 mb-4">
        <div class="fc-card chart-card-animated">
            <div class="fc-card-header"><h3><i class="fas fa-chart-pie" style="color:#0284c7;"></i> Expense by Status</h3></div>
            <div class="fc-card-body padded">
                <div class="chart-wrap" style="height:240px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 mb-4">
        <div class="fc-card chart-card-animated" style="animation-delay:.1s;">
            <div class="fc-card-header"><h3><i class="fas fa-chart-area" style="color:#059669;"></i> Monthly Expense vs Cashflow (6 Months)</h3></div>
            <div class="fc-card-body padded">
                <div class="chart-wrap" style="height:240px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════════════════ --}}
@include('admin.finance.partials.modals')
@foreach($expensePlans as $expense)
    @include('admin.finance.partials.payment-modal', ['expense' => $expense, 'bankAccounts' => $bankAccounts])
@endforeach
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Chart Data ──────────────────────────────────────────────────
const statusLabels  = @json($expenseByStatus->keys()->values());
const statusValues  = @json($expenseByStatus->values()->map(fn($v) => (int) $v)->values());
const expenseLabels = @json($monthlyExpense->keys()->values());
const expenseValues = @json($monthlyExpense->values()->map(fn($v) => (float) $v)->values());
const cashflowLabels= @json($monthlyCashflow->keys()->values());
const cashflowValues= @json($monthlyCashflow->values()->map(fn($v) => (float) $v)->values());

// Merge labels
const allMonthLabels = [...new Set([...expenseLabels, ...cashflowLabels])].sort();

// ── Pie Chart ──────────────────────────────────────────────────
const pieColors = {
    draft:     '#94a3b8',
    submitted: '#3b82f6',
    approved:  '#059669',
    partial:   '#d97706',
    paid:      '#16a34a',
    deferred:  '#7c3aed',
    rejected:  '#dc2626',
    cancelled: '#475569',
    received:  '#14b8a6',
};
const statusChart = new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusValues,
            backgroundColor: statusLabels.map(l => pieColors[l] || '#94a3b8'),
            borderWidth: 4,
            borderColor: '#ffffff',
            hoverOffset: 14,
            spacing: 3,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        rotation: -90,
        animation: {
            animateRotate: true,
            animateScale: true,
            duration: 1400,
            easing: 'easeOutQuart',
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { size: 11 }, padding: 12, usePointStyle: true },
            },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed} plan${ctx.parsed !== 1 ? 's' : ''}`,
                }
            }
        },
    }
});
setInterval(() => {
    if (!statusChart.data.datasets[0].data.length) return;
    statusChart.options.rotation = (statusChart.options.rotation || 0) + 1;
    statusChart.update('none');
}, 80);

// ── Trend Chart ────────────────────────────────────────────────
const expMap  = Object.fromEntries(expenseLabels.map((l,i)  => [l, expenseValues[i]]));
const cashMap = Object.fromEntries(cashflowLabels.map((l,i) => [l, cashflowValues[i]]));

const trendChart = new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: allMonthLabels,
        datasets: [
            {
                label: 'Planned Expense',
                data: allMonthLabels.map(m => expMap[m] || 0),
                borderColor: '#dc2626',
                backgroundColor: ctx => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
                    g.addColorStop(0, 'rgba(220,38,38,.18)');
                    g.addColorStop(1, 'rgba(220,38,38,.01)');
                    return g;
                },
                fill: true, tension: .48, borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#dc2626',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
            },
            {
                label: 'Expected Cashflow',
                data: allMonthLabels.map(m => cashMap[m] || 0),
                borderColor: '#059669',
                backgroundColor: ctx => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
                    g.addColorStop(0, 'rgba(5,150,105,.15)');
                    g.addColorStop(1, 'rgba(5,150,105,.01)');
                    return g;
                },
                fill: true, tension: .48, borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 1600,
            easing: 'easeOutQuart',
        },
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, usePointStyle: true, padding: 16 } },
            tooltip: {
                backgroundColor: '#0f172a',
                titleColor: '#94a3b8',
                bodyColor: '#f1f5f9',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: ctx => ` ${ctx.dataset.label}: Rs ${Number(ctx.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 0})}`
                }
            }
        },
        scales: {
            x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#64748b' } },
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: {
                    font: { size: 11 }, color: '#64748b',
                    callback: v => 'Rs ' + (v >= 100000 ? (v/100000).toFixed(1) + 'L' : v >= 1000 ? (v/1000).toFixed(0) + 'K' : v),
                }
            }
        }
    }
});
let waveTick = 0;
setInterval(() => {
    waveTick += 0.08;
    trendChart.data.datasets.forEach((dataset, datasetIndex) => {
        dataset.tension = 0.42 + Math.sin(waveTick + datasetIndex) * 0.06;
    });
    trendChart.update('none');
}, 120);
</script>
@endpush



