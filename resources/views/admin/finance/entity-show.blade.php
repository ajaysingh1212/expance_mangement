@extends('admin.layouts.app')

@section('title', $title)
@section('page-title', $title)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ $backRoute }}">Finance</a></li>
    <li class="breadcrumb-item active">{{ $heading }}</li>
@endsection

@section('content')
@php
    $money = fn($amount) => is_numeric($amount) ? 'Rs ' . number_format((float) $amount, 2) : $amount;
@endphp

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h3>{{ $heading }}</h3>
            <div class="text-muted small">{{ $subheading }}</div>
        </div>
        <a href="{{ $backRoute }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-left mr-1"></i> Back</a>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($summary as $label => $value)
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small text-uppercase">{{ $label }}</div>
                        <div class="h5 mb-0">{{ $money($value) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            @foreach($details as $label => $value)
                <div class="col-md-6 mb-2">
                    <strong>{{ $label }}:</strong>
                    <span class="text-muted">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

@if($plans->count())
<div class="card mb-3">
    <div class="card-header"><h3><i class="fas fa-list-check mr-2 text-primary"></i>Planning Details</h3></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Ledger / Party</th>
                        <th>Bank</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $item)
                        @php
                            $row = $item['row'];
                            $isExpense = $item['type'] === 'Expense';
                            $amount = $isExpense ? ($row->net_amount ?: $row->planned_amount) : $row->expected_amount;
                            $date = $isExpense ? ($row->expense_month ?: $row->due_date) : $row->expected_date;
                        @endphp
                        <tr>
                            <td><span class="badge badge-{{ $isExpense ? 'warning' : 'success' }}">{{ $item['type'] }}</span></td>
                            <td><strong>{{ $row->title }}</strong><div class="text-muted small">{{ $isExpense ? $row->invoice_no : $row->receipt_no }}</div></td>
                            <td>{{ $row->ledger?->name ?: ($row->vendor_name ?? $row->payer_name ?? 'Direct') }}</td>
                            <td>{{ $row->bankAccount?->name ?: '-' }}</td>
                            <td>{{ $money($amount) }}</td>
                            <td>{{ $date instanceof \Carbon\Carbon ? $date->format('d M Y') : ($date ?: '-') }}</td>
                            <td><span class="badge badge-light">{{ ucfirst($row->status) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header"><h3><i class="fas fa-file-lines mr-2 text-success"></i>Statement</h3></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Txn No.</th>
                        <th>Account</th>
                        <th>Party</th>
                        <th>Description</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance After</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                        <tr>
                            <td>{{ $txn->transaction_date?->format('d M Y') }}</td>
                            <td>{{ $txn->transaction_no }}</td>
                            <td>{{ $txn->bankAccount?->name ?: '-' }}</td>
                            <td>{{ $txn->party_name ?: '-' }}</td>
                            <td>
                                {{ $txn->description ?: '-' }}
                                <div class="text-muted small">{{ $txn->category ?: '-' }} {{ $txn->reference_no ? '- '.$txn->reference_no : '' }}</div>
                            </td>
                            <td class="text-danger">{{ $txn->direction === 'debit' ? $money($txn->amount) : '-' }}</td>
                            <td class="text-success">{{ $txn->direction === 'credit' ? $money($txn->amount) : '-' }}</td>
                            <td>{{ $money($txn->balance_after) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No posted statement transactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">{{ $transactions->links() }}</div>
</div>
@endsection
