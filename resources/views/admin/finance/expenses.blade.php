@extends('admin.layouts.app')

@section('title', 'Expense Planning')
@section('page-title', 'Expense Planning')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Expenses</li>
@endsection

@section('content')
@php $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2); $expenseLedgers = $ledgers; @endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-receipt mr-2 text-warning"></i>Expense & Salary Plans</h3>
        @can('finance.expenses.create')<button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#expenseModal"><i class="fas fa-plus mr-1"></i> Plan Expense</button>@endcan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Invoice</th><th>Ledger</th><th>Month/Due</th><th>Net</th><th>Paid</th><th>Balance</th><th>Status</th><th>Attachment</th><th></th></tr></thead>
                <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td><strong>{{ $expense->title }}</strong><div class="text-muted small">{{ $expense->invoice_no }} · {{ $expense->vendor_name ?: $expense->category }} · {{ $expense->priority }}</div></td>
                    <td>{{ $expense->ledger?->name }}</td>
                    <td>{{ $expense->expense_month ?: '-' }}<div class="text-muted small">{{ $expense->due_date?->format('d M Y') ?: 'No due date' }}</div></td>
                    <td>{{ $money($expense->net_amount ?: $expense->planned_amount) }}</td>
                    <td>{{ $money($expense->paid_amount) }}</td>
                    <td><strong>{{ $money($expense->remaining_amount) }}</strong></td>
                    <td><span class="badge badge-{{ match($expense->status) {'paid'=>'success','partial'=>'warning','approved'=>'info','deferred'=>'secondary','rejected'=>'danger',default=>'light'} }}">{{ ucfirst($expense->status) }}</span></td>
                    <td>@if($expense->attachment_path)<a href="{{ asset('storage/'.$expense->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="far fa-file"></i></a>@else - @endif</td>
                    <td class="text-right">
                        @can('finance.approve')@if(in_array($expense->status, ['submitted', 'draft', 'deferred']))<form action="{{ route('admin.finance.expenses.approve', $expense) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button></form>@endif@endcan
                        @can('finance.payments.create')@if(in_array($expense->status, ['approved', 'partial']) && $expense->remaining_amount > 0)<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paymentModal{{ $expense->id }}"><i class="fas fa-money-bill-wave"></i></button>@endif@endcan
                        @can('finance.expenses.show')<a href="{{ route('admin.finance.expenses.invoice', $expense) }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="fas fa-file-invoice"></i></a>@endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No expense plans found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">{{ $expenses->links() }}</div>
</div>
@include('admin.finance.partials.modals')
@foreach($expenses as $expense)
    @include('admin.finance.partials.payment-modal', ['expense' => $expense, 'bankAccounts' => $bankAccounts])
@endforeach
@endsection
