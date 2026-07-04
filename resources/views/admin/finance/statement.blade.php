@extends('admin.layouts.app')

@section('title', 'Bank Statement')
@section('page-title', 'Bank Statement')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Finance</a></li>
    <li class="breadcrumb-item active">Bank Statement</li>
@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════════
   BANK STATEMENT — Ledger-grade Professional Design
   ═══════════════════════════════════════════════════════════════ */
:root {
    --st-bg:       #f0f4f8;
    --st-card:     #ffffff;
    --st-border:   #e2e8f0;
    --st-text:     #0f172a;
    --st-muted:    #64748b;
    --st-credit:   #059669;
    --st-debit:    #dc2626;
    --st-primary:  #1e40af;
    --st-radius:   12px;
    --st-shadow:   0 1px 3px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.04);
}

body { background: var(--st-bg) !important; }

/* ── Header ── */
.stmt-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #0f172a 100%);
    border-radius: var(--st-radius);
    padding: 26px 30px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}
.stmt-header::before {
    content: '';
    position: absolute; top: -50px; right: -50px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(30,64,175,.4) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.stmt-header .sh-left h1 { color: #fff; font-size: 1.4rem; font-weight: 800; margin: 0 0 4px; letter-spacing: -.01em; }
.stmt-header .sh-left p  { color: #94a3b8; font-size: .82rem; margin: 0; }
.stmt-header .sh-right   { display: flex; gap: 8px; flex-wrap: wrap; }
.btn-stmt {
    font-size: .8rem; font-weight: 700; padding: 9px 18px;
    border-radius: 8px; border: none; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px;
    transition: all .2s ease; text-decoration: none;
}
.btn-stmt:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.25); }
.btn-glass {
    background: rgba(255,255,255,.12);
    color: #fff;
    border: 1px solid rgba(255,255,255,.2) !important;
    backdrop-filter: blur(8px);
}
.btn-glass:hover { background: rgba(255,255,255,.22); color: #fff; }
.btn-entry {
    background: #2563eb; color: #fff;
}
.btn-print {
    background: rgba(255,255,255,.1); color: #fff;
    border: 1px solid rgba(255,255,255,.15) !important;
}

/* ── Filter Bar ── */
.filter-card {
    background: var(--st-card);
    border-radius: var(--st-radius);
    box-shadow: var(--st-shadow);
    border: 1px solid var(--st-border);
    padding: 20px 24px;
    margin-bottom: 20px;
}
.filter-card .form-group label {
    font-size: .72rem; font-weight: 700; color: var(--st-muted);
    text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px;
}
.filter-card .form-control {
    border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: .85rem;
    padding: 8px 12px; color: var(--st-text);
    transition: border-color .2s, box-shadow .2s;
}
.filter-card .form-control:focus {
    border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); outline: none;
}
.btn-filter {
    background: #0f172a; color: #fff; border: none; border-radius: 8px;
    font-size: .82rem; font-weight: 700; padding: 9px 20px;
    display: flex; align-items: center; gap: 6px; cursor: pointer;
    transition: background .2s;
}
.btn-filter:hover { background: #1e3a5f; }
.btn-reset {
    background: #f1f5f9; color: #475569; border: none; border-radius: 8px;
    font-size: .82rem; font-weight: 600; padding: 9px 16px;
    display: flex; align-items: center; gap: 6px; cursor: pointer;
    text-decoration: none; transition: background .2s;
}
.btn-reset:hover { background: #e2e8f0; color: #0f172a; }

/* ── KPI Cards ── */
.kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
@media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px)  { .kpi-grid { grid-template-columns: 1fr; } }

.kpi-card {
    background: var(--st-card);
    border-radius: var(--st-radius);
    box-shadow: var(--st-shadow);
    border: 1px solid var(--st-border);
    padding: 20px 22px;
    position: relative;
    overflow: hidden;
}
.kpi-card .kpi-accent { position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 12px 0 0 12px; }
.kpi-card .kpi-icon {
    width: 44px; height: 44px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; margin-bottom: 14px;
}
.kpi-card .kpi-label { font-size: .72rem; font-weight: 700; color: var(--st-muted); text-transform: uppercase; letter-spacing: .06em; }
.kpi-card .kpi-value { font-size: 1.45rem; font-weight: 800; color: var(--st-text); line-height: 1.2; margin-top: 4px; font-variant-numeric: tabular-nums; }
.kpi-card .kpi-sub   { font-size: .72rem; color: var(--st-muted); margin-top: 5px; }

/* ── Statement Table ── */
.stmt-card {
    background: var(--st-card);
    border-radius: var(--st-radius);
    box-shadow: var(--st-shadow);
    border: 1px solid var(--st-border);
    overflow: hidden;
}
.stmt-card-header {
    padding: 15px 22px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid var(--st-border);
    background: #f8fafc;
}
.stmt-card-header h3 {
    font-size: .9rem; font-weight: 800; color: var(--st-text); margin: 0;
    display: flex; align-items: center; gap: 8px;
}
.stmt-card-header .hdr-actions { display: flex; gap: 8px; align-items: center; }

/* Ledger-style table */
.stmt-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
.stmt-table thead tr {
    background: #f8fafc;
}
.stmt-table thead th {
    padding: 10px 14px;
    font-size: .7rem; font-weight: 700; color: var(--st-muted);
    text-transform: uppercase; letter-spacing: .07em;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}
.stmt-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background .12s;
}
.stmt-table tbody tr:last-child { border-bottom: none; }
.stmt-table tbody tr:hover { background: #f8fafc; }
.stmt-table td { padding: 11px 14px; vertical-align: middle; }

/* Row type stripes */
.row-credit { border-left: 3px solid #059669; }
.row-debit  { border-left: 3px solid #dc2626; }

/* Amount cols */
.amt-debit  { font-weight: 700; color: var(--st-debit);  font-variant-numeric: tabular-nums; }
.amt-credit { font-weight: 700; color: var(--st-credit); font-variant-numeric: tabular-nums; }
.amt-balance{ font-weight: 800; color: var(--st-text);   font-variant-numeric: tabular-nums; font-size: .88rem; }
.amt-blank  { color: #d1d5db; font-size: .8rem; }

/* Direction badge */
.dir-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .68rem; font-weight: 700; padding: 3px 8px;
    border-radius: 99px; letter-spacing: .04em; text-transform: uppercase;
    white-space: nowrap;
}
.dir-cr { background: #dcfce7; color: #15803d; }
.dir-dr { background: #fef2f2; color: #b91c1c; }

/* Reconciliation */
.rec-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .68rem; font-weight: 700; padding: 3px 9px;
    border-radius: 99px; letter-spacing: .03em; text-transform: uppercase;
    cursor: pointer; border: none; transition: all .15s;
}
.rec-ok   { background: #dcfce7; color: #15803d; }
.rec-ok:hover { background: #bbf7d0; }
.rec-pend { background: #fffbeb; color: #b45309; }
.rec-pend:hover { background: #fde68a; }

/* Txn number */
.txn-no { font-family: 'Courier New', monospace; font-size: .75rem; color: #475569; background: #f1f5f9; padding: 2px 7px; border-radius: 5px; }

/* Party / description */
.td-party { font-weight: 600; font-size: .84rem; color: var(--st-text); }
.td-sub   { font-size: .72rem; color: var(--st-muted); margin-top: 2px; }

/* Category pill */
.cat-pill {
    display: inline-block; font-size: .67rem; font-weight: 600;
    padding: 2px 8px; border-radius: 5px; background: #f1f5f9; color: #475569;
    margin-top: 3px; text-transform: capitalize;
}

/* Changed-by tooltip row */
.rec-changed-by { font-size: .7rem; color: var(--st-muted); margin-top: 3px; }

/* Footer */
.stmt-footer {
    padding: 14px 22px;
    background: #f8fafc;
    border-top: 1px solid var(--st-border);
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
}
.stmt-footer .totals { display: flex; gap: 24px; flex-wrap: wrap; }
.stmt-footer .total-item { font-size: .78rem; color: var(--st-muted); }
.stmt-footer .total-item strong { display: block; font-size: .92rem; margin-top: 2px; }
.stmt-footer .total-item.credit strong { color: var(--st-credit); }
.stmt-footer .total-item.debit  strong { color: var(--st-debit); }
.stmt-footer .total-item.net    strong { color: var(--st-text); }

/* Empty state */
.empty-stmt { text-align: center; padding: 60px 20px; }
.empty-stmt i { font-size: 2.5rem; color: #d1d5db; display: block; margin-bottom: 14px; }
.empty-stmt h4 { color: var(--st-text); font-size: 1rem; margin-bottom: 6px; }
.empty-stmt p  { color: var(--st-muted); font-size: .82rem; }

/* ── MODALS ── */
.fin-modal .modal-content {
    border: none; border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
}
.fin-modal .modal-header {
    color: #fff; border-radius: 16px 16px 0 0;
    padding: 18px 24px; border: none;
}
.fin-modal .modal-header h5 { font-size: 1rem; font-weight: 700; margin: 0; }
.fin-modal .modal-header .close { color: #94a3b8; opacity: 1; font-size: 1.3rem; }
.fin-modal .modal-header .close:hover { color: #fff; }
.fin-modal .modal-body { padding: 24px; }
.fin-modal .modal-footer { padding: 16px 24px; background: #f8fafc; border-radius: 0 0 16px 16px; border-top: 1px solid var(--st-border); }
.fin-modal .form-group label { font-size: .75rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px; display: block; }
.fin-modal .form-control {
    border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: .85rem;
    padding: 9px 12px; color: var(--st-text);
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
}
.fin-modal .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); outline: none; }
.net-calc-box {
    background: #0f172a; color: #fff; border-radius: 10px; padding: 14px 18px;
    display: flex; justify-content: space-between; align-items: center; margin-top: 4px;
}
.net-calc-box .nc-label { font-size: .75rem; color: #94a3b8; }
.net-calc-box .nc-value { font-size: 1.2rem; font-weight: 800; }
.balance-info-box {
    border-radius: 9px; padding: 10px 14px; font-size: .82rem; margin-bottom: 16px;
    display: flex; align-items: center; gap: 10px;
}
.bib-credit { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
.bib-debit  { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
.section-label {
    font-size: .7rem; font-weight: 800; color: var(--st-muted);
    text-transform: uppercase; letter-spacing: .08em;
    margin: 4px 0 14px; display: flex; align-items: center; gap: 8px;
}
.section-label::after { content: ''; flex: 1; height: 1px; background: var(--st-border); }

/* Print styles */
@media print {
    body { background: white !important; }
    .stmt-header, .filter-card, .no-print { display: none !important; }
    .stmt-card { box-shadow: none; border: 1px solid #ccc; }
    .stmt-table { font-size: .75rem; }
    .rec-badge  { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .row-credit, .row-debit { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
@endpush

@section('content')
@php
    $money  = fn($v) => 'Rs ' . number_format((float) $v, 2);
    $net    = ($summary['credit'] ?? 0) - ($summary['debit'] ?? 0);

    // Selected account balance
    $selectedBankBalance = null;
    if (request('bank_account_id')) {
        $selectedBankBalance = $bankAccounts->firstWhere('id', request('bank_account_id'));
    }
@endphp

{{-- ══════════════════════════════════════════════════════════════
     HEADER
══════════════════════════════════════════════════════════════ --}}
<div class="stmt-header no-print">
    <div class="sh-left">
        <h1><i class="fas fa-file-lines mr-2" style="color:#38bdf8;"></i>Bank Statement</h1>
        <p>Ledger of all posted transactions · Filter by account &amp; date range</p>
    </div>
    <div class="sh-right">
        @can('finance.approve')
        <button class="btn-stmt btn-entry" data-toggle="modal" data-target="#manualEntryModal">
            <i class="fas fa-pen-to-square"></i> Manual Entry
        </button>
        @endcan
        <button class="btn-stmt btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn-stmt btn-glass">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     FILTER BAR
══════════════════════════════════════════════════════════════ --}}
<div class="filter-card no-print">
    <form method="GET" class="row align-items-end" style="margin:0;">
        <div class="col-md-4 col-sm-6 form-group mb-md-0">
            <label>Bank Account</label>
            <select name="bank_account_id" class="form-control">
                <option value="">All Accounts</option>
                @foreach($bankAccounts as $account)
                <option value="{{ $account->id }}" @selected(request('bank_account_id') == $account->id)>
                    {{ $account->name }} · {{ $money($account->current_balance) }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-sm-6 form-group mb-md-0">
            <label>Direction</label>
            <select name="direction" class="form-control">
                <option value="">All</option>
                <option value="credit" @selected(request('direction') === 'credit')>Credit Only</option>
                <option value="debit"  @selected(request('direction') === 'debit')>Debit Only</option>
            </select>
        </div>
        <div class="col-md-2 col-sm-6 form-group mb-md-0">
            <label>From</label>
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-2 col-sm-6 form-group mb-md-0">
            <label>To</label>
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-2 form-group mb-md-0">
            <label>&nbsp;</label>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn-filter" style="flex:1;">
                    <i class="fas fa-search"></i> Apply
                </button>
                <a href="{{ route('admin.finance.statement.index') }}" class="btn-reset">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ══════════════════════════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════════════════════════ --}}
<div class="kpi-grid">

    {{-- Bank Balance --}}
    <div class="kpi-card">
        <div class="kpi-accent" style="background:#1e40af;"></div>
        <div class="kpi-icon" style="background:#dbeafe;color:#1e40af;">
            <i class="fas fa-building-columns"></i>
        </div>
        <div class="kpi-label">
            {{ $selectedBankBalance ? $selectedBankBalance->name : 'Total Bank Balance' }}
        </div>
        <div class="kpi-value" style="color:#1e40af;">
            {{ $money($selectedBankBalance ? $selectedBankBalance->current_balance : $bankAccounts->sum('current_balance')) }}
        </div>
        <div class="kpi-sub">
            @if($selectedBankBalance)
                Opening: {{ $money($selectedBankBalance->opening_balance) }}
            @else
                {{ $bankAccounts->count() }} active account{{ $bankAccounts->count() != 1 ? 's' : '' }}
            @endif
        </div>
    </div>

    {{-- Credits --}}
    <div class="kpi-card">
        <div class="kpi-accent" style="background:#059669;"></div>
        <div class="kpi-icon" style="background:#dcfce7;color:#059669;">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="kpi-label">Credits (This Page)</div>
        <div class="kpi-value" style="color:#059669;">{{ $money($summary['credit'] ?? 0) }}</div>
        <div class="kpi-sub">Money received / inflow</div>
    </div>

    {{-- Debits --}}
    <div class="kpi-card">
        <div class="kpi-accent" style="background:#dc2626;"></div>
        <div class="kpi-icon" style="background:#fef2f2;color:#dc2626;">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div class="kpi-label">Debits (This Page)</div>
        <div class="kpi-value" style="color:#dc2626;">{{ $money($summary['debit'] ?? 0) }}</div>
        <div class="kpi-sub">Payments made / outflow</div>
    </div>

    {{-- Net Movement --}}
    <div class="kpi-card">
        <div class="kpi-accent" style="background:{{ $net >= 0 ? '#059669' : '#dc2626' }};"></div>
        <div class="kpi-icon" style="background:{{ $net >= 0 ? '#dcfce7' : '#fef2f2' }};color:{{ $net >= 0 ? '#059669' : '#dc2626' }};">
            <i class="fas fa-scale-balanced"></i>
        </div>
        <div class="kpi-label">Net Movement</div>
        <div class="kpi-value" style="color:{{ $net >= 0 ? '#059669' : '#dc2626' }};">
            {{ $net >= 0 ? '+' : '' }}{{ $money($net) }}
        </div>
        <div class="kpi-sub">Credit − Debit on page</div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     STATEMENT TABLE
══════════════════════════════════════════════════════════════ --}}
<div class="stmt-card">
    <div class="stmt-card-header">
        <h3>
            <i class="fas fa-table-list" style="color:#1e40af;"></i>
            Posted Transactions
            <span style="font-size:.75rem;font-weight:400;color:var(--st-muted);">
                ({{ $transactions->total() }} total · page {{ $transactions->currentPage() }})
            </span>
        </h3>
        <div class="hdr-actions no-print">
            @can('finance.approve')
            <span style="font-size:.75rem;color:var(--st-muted);">
                <i class="fas fa-circle-info mr-1"></i>Click reconciliation status to toggle
            </span>
            @endcan
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="stmt-table">
            <thead>
                <tr>
                    <th style="width:100px;">Date</th>
                    <th style="width:140px;">Txn No.</th>
                    <th>Account</th>
                    <th>Party / Narration</th>
                    <th>Category</th>
                    <th style="text-align:center;">Type</th>
                    <th style="text-align:right;">Debit (Dr)</th>
                    <th style="text-align:right;">Credit (Cr)</th>
                    <th style="text-align:right;">Balance</th>
                    <th style="text-align:center;">Reconciliation</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transactions as $txn)
            <tr class="row-{{ $txn->direction }}">

                {{-- Date --}}
                <td>
                    <div style="font-weight:600;font-size:.82rem;">{{ $txn->transaction_date?->format('d M Y') }}</div>
                    <div style="font-size:.7rem;color:var(--st-muted);">{{ $txn->transaction_date?->format('D') }}</div>
                </td>

                {{-- Txn No --}}
                <td>
                    <span class="txn-no">{{ $txn->transaction_no }}</span>
                </td>

                {{-- Account --}}
                <td>
                    <div style="font-size:.82rem;font-weight:600;">{{ $txn->bankAccount?->name }}</div>
                    <div style="font-size:.7rem;color:var(--st-muted);">{{ ucfirst($txn->bankAccount?->type ?? '') }}</div>
                </td>

                {{-- Party + Narration --}}
                <td style="max-width:220px;">
                    <div class="td-party">{{ $txn->party_name ?: '—' }}</div>
                    @if($txn->description)
                    <div class="td-sub">{{ $txn->description }}</div>
                    @endif
                    @if($txn->reference_no)
                    <div style="font-size:.7rem;color:#94a3b8;margin-top:2px;">
                        <i class="fas fa-hashtag" style="font-size:.65rem;"></i> {{ $txn->reference_no }}
                    </div>
                    @endif
                </td>

                {{-- Category --}}
                <td>
                    @if($txn->category)
                    <span class="cat-pill">{{ $txn->category }}</span>
                    @else
                    <span style="color:#d1d5db;font-size:.8rem;">—</span>
                    @endif
                </td>

                {{-- Direction --}}
                <td style="text-align:center;">
                    <span class="dir-badge {{ $txn->direction === 'credit' ? 'dir-cr' : 'dir-dr' }}">
                        <i class="fas fa-arrow-{{ $txn->direction === 'credit' ? 'down' : 'up' }}"></i>
                        {{ strtoupper($txn->direction) }}
                    </span>
                </td>

                {{-- Debit --}}
                <td style="text-align:right;">
                    @if($txn->direction === 'debit')
                        <span class="amt-debit">{{ $money($txn->amount) }}</span>
                    @else
                        <span class="amt-blank">—</span>
                    @endif
                </td>

                {{-- Credit --}}
                <td style="text-align:right;">
                    @if($txn->direction === 'credit')
                        <span class="amt-credit">{{ $money($txn->amount) }}</span>
                    @else
                        <span class="amt-blank">—</span>
                    @endif
                </td>

                {{-- Balance After --}}
                <td style="text-align:right;">
                    <span class="amt-balance">{{ $money($txn->balance_after) }}</span>
                </td>

                {{-- Reconciliation --}}
                <td style="text-align:center;">
                    @can('finance.approve')
                    <form action="{{ route('admin.finance.transactions.reconcile', $txn) }}" method="POST" style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="rec-badge {{ $txn->reconciliation_status === 'reconciled' ? 'rec-ok' : 'rec-pend' }}" title="Click to toggle">
                            <i class="fas fa-{{ $txn->reconciliation_status === 'reconciled' ? 'check-circle' : 'clock' }}"></i>
                            {{ $txn->reconciliation_status === 'reconciled' ? 'Reconciled' : 'Unreconciled' }}
                        </button>
                    </form>
                    @else
                    <span class="rec-badge {{ $txn->reconciliation_status === 'reconciled' ? 'rec-ok' : 'rec-pend' }}">
                        <i class="fas fa-{{ $txn->reconciliation_status === 'reconciled' ? 'check-circle' : 'clock' }}"></i>
                        {{ $txn->reconciliation_status === 'reconciled' ? 'Reconciled' : 'Unreconciled' }}
                    </span>
                    @endcan
                    @if($txn->reconciledBy ?? null)
                    <div class="rec-changed-by">
                        by {{ $txn->reconciledBy?->name }}<br>
                        <span style="font-size:.65rem;">{{ $txn->reconciled_at?->format('d M Y, h:i A') }}</span>
                    </div>
                    @elseif($txn->creator ?? null)
                    <div class="rec-changed-by">
                        posted by {{ $txn->creator?->name }}
                    </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10">
                    <div class="empty-stmt">
                        <i class="fas fa-file-lines"></i>
                        <h4>No transactions found</h4>
                        <p>Try changing the filters, or post a manual entry above.</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer: Totals + Pagination --}}
    <div class="stmt-footer">
        <div class="totals">
            <div class="total-item credit">
                <span>Total Credits (Page)</span>
                <strong>{{ $money($summary['credit'] ?? 0) }}</strong>
            </div>
            <div class="total-item debit">
                <span>Total Debits (Page)</span>
                <strong>{{ $money($summary['debit'] ?? 0) }}</strong>
            </div>
            <div class="total-item net">
                <span>Net Movement</span>
                <strong style="color:{{ $net >= 0 ? 'var(--st-credit)' : 'var(--st-debit)' }};">
                    {{ $net >= 0 ? '+' : '' }}{{ $money($net) }}
                </strong>
            </div>
        </div>
        <div class="no-print">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MANUAL BANK ENTRY MODAL
══════════════════════════════════════════════════════════════ --}}
@can('finance.approve')
<div class="modal fade fin-modal" id="manualEntryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.finance.bank-accounts.manual-entry') }}">
            @csrf
            <div class="modal-header" style="background: linear-gradient(135deg,#1e3a5f,#1e40af);">
                <h5><i class="fas fa-pen-to-square mr-2"></i> Manual Bank Entry</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                {{-- Info Note --}}
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:9px;padding:12px 16px;font-size:.82rem;color:#92400e;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;">
                    <i class="fas fa-triangle-exclamation" style="margin-top:2px;flex-shrink:0;"></i>
                    <div>
                        Use this for entries <strong>not linked to any cashflow or expense</strong> — bank service charges, interest income, corrections, opening adjustments, etc. This will directly update the account balance.
                    </div>
                </div>

                {{-- Account & Type --}}
                <div class="section-label">Account &amp; Entry Type</div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Bank Account *</label>
                        <select name="bank_account_id" class="form-control" id="me_bankSelect" required>
                            <option value="">— Select Account —</option>
                            @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}" data-balance="{{ $account->current_balance }}" data-name="{{ $account->name }}">
                                {{ $account->name }} · Rs {{ number_format((float)$account->current_balance, 2) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Entry Type *</label>
                        <select name="direction" class="form-control" id="me_direction" required>
                            <option value="debit">Debit — Money going out (Bank charges, corrections)</option>
                            <option value="credit">Credit — Money coming in (Interest, refund, correction)</option>
                        </select>
                    </div>
                </div>

                {{-- Balance Preview --}}
                <div id="me_balanceBox" style="display:none;margin-bottom:16px;">
                    <div class="balance-info-box bib-debit" id="me_balanceInfo">
                        <i class="fas fa-building-columns"></i>
                        <span id="me_balanceText"></span>
                    </div>
                </div>

                {{-- Amount & Date --}}
                <div class="section-label">Amount &amp; Date</div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Amount *</label>
                        <div style="display:flex;border:1.5px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                            <span style="background:#f1f5f9;padding:9px 12px;font-size:.8rem;font-weight:700;color:#64748b;border-right:1px solid #e2e8f0;">Rs</span>
                            <input name="amount" type="number" min="0.01" step="0.01" id="me_amount"
                                   class="form-control" style="border:none;border-radius:0;" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Transaction Date *</label>
                        <input name="transaction_date" type="date" class="form-control"
                               value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Category</label>
                        <input name="category" class="form-control" list="me_categoryList"
                               placeholder="Bank charges, Interest...">
                        <datalist id="me_categoryList">
                            <option value="Bank Charges">
                            <option value="Service Fee">
                            <option value="Interest Income">
                            <option value="Interest Expense">
                            <option value="GST on Bank Charges">
                            <option value="Correction Entry">
                            <option value="Refund">
                            <option value="Other">
                        </datalist>
                    </div>
                </div>

                {{-- Net Calc Preview --}}
                <div class="net-calc-box mb-4" id="me_previewBox" style="display:none;">
                    <div>
                        <div class="nc-label" id="me_previewLabel">Debit — Amount going out</div>
                        <div style="font-size:.72rem;color:#64748b;margin-top:2px;" id="me_balanceAfterPreview"></div>
                    </div>
                    <span class="nc-value" id="me_previewAmt">Rs 0.00</span>
                </div>

                {{-- Party & Reference --}}
                <div class="section-label">Party &amp; Reference</div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Party Name</label>
                        <input name="party_name" class="form-control" placeholder="Bank name, counterparty...">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Reference No. / UTR</label>
                        <input name="reference_no" class="form-control" placeholder="Cheque / UTR / TXN ID">
                    </div>
                </div>

                {{-- Narration --}}
                <div class="section-label">Narration</div>
                <div class="form-group">
                    <label>Narration / Description *</label>
                    <textarea name="description" class="form-control" rows="3" required
                              placeholder="e.g. HDFC Bank quarterly service charge for Q1 2025, adjusted against account balance..."></textarea>
                    <small style="font-size:.72rem;color:var(--st-muted);">This appears in the statement as the transaction description.</small>
                </div>

            </div>
            <div class="modal-footer">
                <div style="font-size:.75rem;color:var(--st-muted);margin-right:auto;">
                    <i class="fas fa-shield-halved mr-1"></i>Only approvers can post manual entries
                </div>
                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:8px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="border-radius:8px;font-weight:700;padding:9px 22px;">
                    <i class="fas fa-paper-plane mr-1"></i> Post Entry
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script>
// ── Manual Entry: Live Balance & Preview ────────────────────────
(function () {
    const bankSel   = document.getElementById('me_bankSelect');
    const dirSel    = document.getElementById('me_direction');
    const amtInput  = document.getElementById('me_amount');
    const balBox    = document.getElementById('me_balanceBox');
    const balInfo   = document.getElementById('me_balanceInfo');
    const balText   = document.getElementById('me_balanceText');
    const prevBox   = document.getElementById('me_previewBox');
    const prevLabel = document.getElementById('me_previewLabel');
    const prevAmt   = document.getElementById('me_previewAmt');
    const prevAfter = document.getElementById('me_balanceAfterPreview');

    if (!bankSel) return;

    function fmt(v) {
        return 'Rs ' + Math.abs(Number(v)).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function update() {
        const opt     = bankSel.selectedOptions[0];
        const balance = parseFloat(opt?.dataset.balance || 0);
        const name    = opt?.dataset.name || '';
        const dir     = dirSel?.value;
        const amt     = parseFloat(amtInput?.value || 0);

        // Balance box
        if (opt && opt.value) {
            balBox.style.display = 'block';
            balInfo.className    = 'balance-info-box ' + (dir === 'credit' ? 'bib-credit' : 'bib-debit');
            balText.innerHTML    = `<strong>${name}</strong> — Current Balance: <strong>${fmt(balance)}</strong>`;
        } else {
            balBox.style.display = 'none';
        }

        // Preview box
        if (amt > 0 && opt && opt.value) {
            prevBox.style.display  = 'flex';
            const isDebit          = dir === 'debit';
            const afterBalance     = isDebit ? balance - amt : balance + amt;
            prevLabel.textContent  = isDebit ? 'Debit — Amount going out' : 'Credit — Amount coming in';
            prevAmt.textContent    = fmt(amt);
            prevAmt.style.color    = isDebit ? '#f87171' : '#34d399';
            prevAfter.textContent  = `Balance after: ${fmt(afterBalance)}${isDebit && afterBalance < 0 ? ' ⚠ Will go negative' : ''}`;
        } else {
            prevBox.style.display = 'none';
        }
    }

    bankSel.addEventListener('change', update);
    dirSel?.addEventListener('change', update);
    amtInput?.addEventListener('input', update);
})();
</script>
@endpush

