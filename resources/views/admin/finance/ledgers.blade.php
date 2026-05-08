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
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>Code</th><th>Type</th><th>Default Amount</th><th>Status</th><th>Contact</th></tr></thead>
                <tbody>
                @forelse($ledgers as $ledger)
                    <tr><td><strong>{{ $ledger->name }}</strong><div class="text-muted small">{{ $ledger->description }}</div></td><td>{{ $ledger->code ?: '-' }}</td><td><span class="badge badge-light">{{ ucfirst($ledger->type) }}</span></td><td>{{ $money($ledger->default_amount) }}</td><td><span class="badge badge-{{ $ledger->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($ledger->status) }}</span></td><td>{{ $ledger->phone ?: $ledger->email ?: '-' }}</td></tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No ledgers found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">{{ $ledgers->links() }}</div>
</div>
@include('admin.finance.partials.modals')
@endsection
