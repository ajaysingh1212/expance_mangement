@extends('admin.layouts.app')

@section('title', 'Cashflow Planning')
@section('page-title', 'Cashflow Planning')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Cashflow</li>
@endsection

@section('content')
@php $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2); $incomeLedgers = $ledgers; @endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-arrow-trend-up mr-2 text-success"></i>Cashflow Plans</h3>
        @can('finance.cashflows.create')<button class="btn btn-success btn-sm" data-toggle="modal" data-target="#cashflowModal"><i class="fas fa-plus mr-1"></i> Plan Cash In</button>@endcan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Receipt</th><th>Source</th><th>Bank</th><th>Expected</th><th>Date</th><th>Status</th><th>Attachment</th><th></th></tr></thead>
                <tbody>
                @forelse($cashflows as $cashflow)
                <tr>
                    <td><strong>{{ $cashflow->title }}</strong><div class="text-muted small">{{ $cashflow->receipt_no }} · {{ $cashflow->notes }}</div></td>
                    <td>{{ $cashflow->payer_name ?: $cashflow->ledger?->name ?: 'Direct' }}</td>
                    <td>{{ $cashflow->bankAccount?->name }}</td>
                    <td>{{ $money($cashflow->expected_amount) }}</td>
                    <td>{{ $cashflow->expected_date?->format('d M Y') }}</td>
                    <td><span class="badge badge-{{ $cashflow->status === 'received' ? 'success' : ($cashflow->status === 'rejected' ? 'danger' : 'light') }}">{{ ucfirst($cashflow->status) }}</span></td>
                    <td>@if($cashflow->attachment_path)<a href="{{ asset('storage/'.$cashflow->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="far fa-file"></i></a>@else - @endif</td>
                    <td class="text-right">
                        @can('finance.approve')
                        @if(!in_array($cashflow->status, ['received', 'approved']))
                        <form action="{{ route('admin.finance.cashflows.approve', $cashflow) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success" title="Approve"><i class="fas fa-check"></i></button></form>
                        @elseif($cashflow->status === 'approved')
                        <form action="{{ route('admin.finance.cashflows.receive', $cashflow) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="received_date" value="{{ now()->toDateString() }}">
                            <input type="hidden" name="reference_no" value="{{ $cashflow->reference_no }}">
                            <button class="btn btn-sm btn-primary" title="Confirm received"><i class="fas fa-circle-check"></i></button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No cashflow plans found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">{{ $cashflows->links() }}</div>
</div>
@include('admin.finance.partials.modals')
@endsection
