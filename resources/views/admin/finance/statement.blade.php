@extends('admin.layouts.app')

@section('title', 'Bank Statement')
@section('page-title', 'Bank Statement')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Bank Statement</li>
@endsection

@section('content')
@php $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2); @endphp

<div class="card mb-3">
    <div class="card-header"><h3><i class="fas fa-filter mr-2 text-primary"></i>Statement Filters</h3></div>
    <div class="card-body">
        <form method="GET" class="row align-items-end">
            <div class="col-md-4 form-group">
                <label>Bank Account</label>
                <select name="bank_account_id" class="form-control">
                    <option value="">All Accounts</option>
                    @foreach($bankAccounts as $account)
                    <option value="{{ $account->id }}" @selected(request('bank_account_id') == $account->id)>{{ $account->name }} · {{ $money($account->current_balance) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 form-group"><label>From</label><input type="date" name="from" class="form-control" value="{{ request('from') }}"></div>
            <div class="col-md-3 form-group"><label>To</label><input type="date" name="to" class="form-control" value="{{ request('to') }}"></div>
            <div class="col-md-2 form-group"><button class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Apply</button></div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3"><div class="info-box"><span class="info-box-icon" style="background:#16a34a;color:white;"><i class="fas fa-arrow-down"></i></span><div class="info-box-content"><span class="info-box-text">Credits On Page</span><span class="info-box-number">{{ $money($summary['credit']) }}</span></div></div></div>
    <div class="col-md-4 mb-3"><div class="info-box"><span class="info-box-icon" style="background:#dc2626;color:white;"><i class="fas fa-arrow-up"></i></span><div class="info-box-content"><span class="info-box-text">Debits On Page</span><span class="info-box-number">{{ $money($summary['debit']) }}</span></div></div></div>
    <div class="col-md-4 mb-3"><div class="info-box"><span class="info-box-icon" style="background:#0f172a;color:white;"><i class="fas fa-scale-balanced"></i></span><div class="info-box-content"><span class="info-box-text">Net Movement</span><span class="info-box-number">{{ $money($summary['credit'] - $summary['debit']) }}</span></div></div></div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-file-lines mr-2 text-success"></i>Posted Transactions</h3>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print mr-1"></i> Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Date</th><th>Txn No</th><th>Account</th><th>Party</th><th>Description</th><th>Debit</th><th>Credit</th><th>Balance</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date?->format('d M Y') }}</td>
                    <td><span class="badge badge-light">{{ $transaction->transaction_no }}</span></td>
                    <td>{{ $transaction->bankAccount?->name }}</td>
                    <td>{{ $transaction->party_name ?: '-' }}<div class="text-muted small">{{ $transaction->reference_no }}</div></td>
                    <td>{{ $transaction->description }}<div class="text-muted small">{{ $transaction->category }}</div></td>
                    <td class="text-danger">{{ $transaction->direction === 'debit' ? $money($transaction->amount) : '-' }}</td>
                    <td class="text-success">{{ $transaction->direction === 'credit' ? $money($transaction->amount) : '-' }}</td>
                    <td><strong>{{ $money($transaction->balance_after) }}</strong></td>
                    <td><span class="badge badge-{{ $transaction->reconciliation_status === 'reconciled' ? 'success' : 'warning' }}">{{ ucfirst($transaction->reconciliation_status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No posted transactions found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">{{ $transactions->links() }}</div>
</div>
@endsection
