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
            <table class="table mb-0 datatable">
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
                        @can('finance.cashflows.show')<a href="{{ route('admin.finance.cashflows.show', $cashflow) }}" class="btn btn-sm btn-outline-dark" title="View statement"><i class="fas fa-eye"></i></a>@endcan
                        @can('finance.cashflows.edit')@if($cashflow->status !== 'received')<button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editCashflow{{ $cashflow->id }}" title="Edit"><i class="fas fa-pen"></i></button>@endif@endcan
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
                        @can('finance.cashflows.delete')<button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteCashflow{{ $cashflow->id }}" title="Delete"><i class="fas fa-trash"></i></button>@endcan
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
@foreach($cashflows as $cashflow)
<div class="modal fade fin-modal" id="editCashflow{{ $cashflow->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.finance.cashflows.update', $cashflow) }}">
            @csrf @method('PUT')
            <div class="modal-header" style="background:linear-gradient(135deg,#064e3b,#059669);"><h5><i class="fas fa-pen mr-2"></i>Edit Cash In</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group"><label>Source Ledger</label><select name="ledger_id" class="form-control"><option value="">Direct / Other</option>@foreach($ledgers as $ledger)<option value="{{ $ledger->id }}" @selected($cashflow->ledger_id === $ledger->id)>{{ $ledger->name }} ({{ ucfirst($ledger->type) }})</option>@endforeach</select></div>
                    <div class="col-md-6 form-group"><label>Title *</label><input name="title" class="form-control" required value="{{ $cashflow->title }}"></div>
                    <div class="col-md-6 form-group"><label>Payer Name</label><input name="payer_name" class="form-control" value="{{ $cashflow->payer_name }}"></div>
                    <div class="col-md-6 form-group"><label>Destination Bank *</label><select name="bank_account_id" class="form-control" required>@foreach($bankAccounts as $account)<option value="{{ $account->id }}" @selected($cashflow->bank_account_id === $account->id)>{{ $account->name }} - {{ $money($account->current_balance) }}</option>@endforeach</select></div>
                    <div class="col-md-4 form-group"><label>Expected Amount *</label><input name="expected_amount" type="number" min="1" step="0.01" class="form-control" required value="{{ $cashflow->expected_amount }}"></div>
                    <div class="col-md-4 form-group"><label>Expected Date *</label><input name="expected_date" type="date" class="form-control" required value="{{ $cashflow->expected_date?->toDateString() }}"></div>
                    <div class="col-md-4 form-group"><label>Status *</label><select name="status" class="form-control" required>@foreach(['draft','submitted','approved','rejected','cancelled'] as $status)<option value="{{ $status }}" @selected($cashflow->status === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                    <div class="col-md-6 form-group"><label>Reference No.</label><input name="reference_no" class="form-control" value="{{ $cashflow->reference_no }}"></div>
                    <div class="col-md-6 form-group"><label>Notes</label><input name="notes" class="form-control" value="{{ $cashflow->notes }}"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button class="btn btn-success">Update</button></div>
        </form>
    </div>
</div>
<div class="modal fade fin-modal" id="deleteCashflow{{ $cashflow->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.finance.cashflows.destroy', $cashflow) }}">
            @csrf @method('DELETE')
            <div class="modal-header bg-danger"><h5><i class="fas fa-trash mr-2"></i>Delete Cash In</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <p>Delete <strong>{{ $cashflow->title }}</strong>?</p>
                <div class="form-check mb-2"><input class="form-check-input" type="radio" name="transaction_action" value="keep" checked id="cfKeep{{ $cashflow->id }}"><label class="form-check-label" for="cfKeep{{ $cashflow->id }}">Transaction rakhna hai</label></div>
                <div class="form-check"><input class="form-check-input" type="radio" name="transaction_action" value="delete_revert" id="cfRevert{{ $cashflow->id }}"><label class="form-check-label" for="cfRevert{{ $cashflow->id }}">Transaction delete karke bank balance revert karna hai</label></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button class="btn btn-danger">Delete</button></div>
        </form>
    </div>
</div>
@endforeach
@endsection

