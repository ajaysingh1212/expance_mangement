@extends('admin.layouts.app')

@section('title', 'Ledgers')
@section('page-title', 'Ledgers')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Ledgers</li>
@endsection

@section('content')
@php $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2); @endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-book mr-2 text-primary"></i>Ledger Master</h3>
        @can('finance.ledgers.create')<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ledgerModal"><i class="fas fa-plus mr-1"></i> New Ledger</button>@endcan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 datatable">
                <thead><tr><th>Name</th><th>Code</th><th>Type</th><th>Default Amount</th><th>Status</th><th>Contact</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                @forelse($ledgers as $ledger)
                    <tr>
                        <td><strong>{{ $ledger->name }}</strong><div class="text-muted small">{{ $ledger->description }}</div></td>
                        <td>{{ $ledger->code ?: '-' }}</td>
                        <td><span class="badge badge-light">{{ ucfirst($ledger->type) }}</span></td>
                        <td>{{ $money($ledger->default_amount) }}</td>
                        <td><span class="badge badge-{{ $ledger->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($ledger->status) }}</span></td>
                        <td>{{ $ledger->phone ?: $ledger->email ?: '-' }}</td>
                        <td class="text-right">
                            @can('finance.ledgers.show')<a href="{{ route('admin.finance.ledgers.show', $ledger) }}" class="btn btn-sm btn-outline-dark" title="View statement"><i class="fas fa-eye"></i></a>@endcan
                            @can('finance.ledgers.edit')<button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editLedger{{ $ledger->id }}" title="Edit"><i class="fas fa-pen"></i></button>@endcan
                            @can('finance.ledgers.delete')<button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteLedger{{ $ledger->id }}" title="Delete"><i class="fas fa-trash"></i></button>@endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No ledgers found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">{{ $ledgers->links() }}</div>
</div>
@include('admin.finance.partials.modals')
@foreach($ledgers as $ledger)
<div class="modal fade fin-modal" id="editLedger{{ $ledger->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.finance.ledgers.update', $ledger) }}">
            @csrf @method('PUT')
            <div class="modal-header"><h5><i class="fas fa-pen mr-2"></i>Edit Ledger</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group"><label>Name *</label><input name="name" class="form-control" required value="{{ $ledger->name }}"></div>
                    <div class="col-md-3 form-group"><label>Code</label><input name="code" class="form-control" value="{{ $ledger->code }}"></div>
                    <div class="col-md-3 form-group"><label>Type *</label><select name="type" class="form-control" required>@foreach(['income','expense','salary','vendor','customer','bank','other'] as $type)<option value="{{ $type }}" @selected($ledger->type === $type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
                    <div class="col-md-4 form-group"><label>Default Amount</label><input name="default_amount" type="number" step="0.01" min="0" class="form-control" value="{{ $ledger->default_amount }}"></div>
                    <div class="col-md-4 form-group"><label>Phone</label><input name="phone" class="form-control" value="{{ $ledger->phone }}"></div>
                    <div class="col-md-4 form-group"><label>Email</label><input name="email" type="email" class="form-control" value="{{ $ledger->email }}"></div>
                    <div class="col-md-4 form-group"><label>Status *</label><select name="status" class="form-control" required><option value="active" @selected($ledger->status === 'active')>Active</option><option value="inactive" @selected($ledger->status === 'inactive')>Inactive</option></select></div>
                    <div class="col-md-8 form-group"><label>Contact Person</label><input name="contact_person" class="form-control" value="{{ $ledger->contact_person }}"></div>
                    <div class="col-12 form-group"><label>Description</label><textarea name="description" rows="2" class="form-control">{{ $ledger->description }}</textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update</button></div>
        </form>
    </div>
</div>
<div class="modal fade fin-modal" id="deleteLedger{{ $ledger->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.finance.ledgers.destroy', $ledger) }}">
            @csrf @method('DELETE')
            <div class="modal-header bg-danger"><h5><i class="fas fa-trash mr-2"></i>Delete Ledger</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <p>Delete <strong>{{ $ledger->name }}</strong>?</p>
                <div class="form-check mb-2"><input class="form-check-input" type="radio" name="transaction_action" value="keep" checked id="ledgerKeep{{ $ledger->id }}"><label class="form-check-label" for="ledgerKeep{{ $ledger->id }}">Transactions/plans rakhna hai</label></div>
                <div class="form-check"><input class="form-check-input" type="radio" name="transaction_action" value="delete_revert" id="ledgerRevert{{ $ledger->id }}"><label class="form-check-label" for="ledgerRevert{{ $ledger->id }}">Related transactions delete karke balance revert karna hai</label></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button class="btn btn-danger">Delete</button></div>
        </form>
    </div>
</div>
@endforeach
@endsection

