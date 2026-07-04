    @extends('admin.layouts.app')

    @section('title', 'Bank Accounts')
    @section('page-title', 'Bank & Cash')

    @section('breadcrumbs')
        <li class="breadcrumb-item active">Bank & Cash</li>
    @endsection

    @section('content')
    @php $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2); @endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-building-columns mr-2 text-success"></i>Bank & Cash Accounts</h3>
            @can('finance.bank.create')<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#bankModal"><i class="fas fa-plus mr-1"></i> New Account</button>@endcan
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($bankAccounts as $account)
                <div class="col-md-6 col-xl-4 mb-3">
                    <div class="card h-100" style="border-left:4px solid #0f766e;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between"><h5 class="mb-1">{{ $account->name }}</h5><span class="badge badge-{{ $account->status === 'active' ? 'success' : 'secondary' }}">{{ $account->status }}</span></div>
                            <div class="text-muted small mb-3">{{ $account->bank_name ?: ucfirst($account->type) }} · {{ $account->account_number ?: 'No account number' }}</div>
                            <div class="h4 mb-0">{{ $money($account->current_balance) }}</div>
                            <div class="text-muted small">Opening: {{ $money($account->opening_balance) }}</div>
                            <div class="mt-3">
                                @can('finance.bank.show')<a href="{{ route('admin.finance.bank-accounts.show', $account) }}" class="btn btn-sm btn-outline-dark"><i class="fas fa-eye"></i> View</a>@endcan
                                @can('finance.bank.edit')<button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editBank{{ $account->id }}"><i class="fas fa-pen"></i> Edit</button>@endcan
                                @can('finance.bank.delete')<button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteBank{{ $account->id }}"><i class="fas fa-trash"></i></button>@endcan
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center text-muted py-5">No accounts found.</div>
                @endforelse
            </div>
            {{ $bankAccounts->links() }}
        </div>
    </div>
    @include('admin.finance.partials.modals')
    @foreach($bankAccounts as $account)
    <div class="modal fade fin-modal" id="editBank{{ $account->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('admin.finance.bank-accounts.update', $account) }}">
                @csrf @method('PUT')
                <div class="modal-header"><h5><i class="fas fa-pen mr-2"></i>Edit Account</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Account Name *</label><input name="name" class="form-control" required value="{{ $account->name }}"></div>
                        <div class="col-md-3 form-group"><label>Type *</label><select name="type" class="form-control" required>@foreach(['bank','cash','wallet'] as $type)<option value="{{ $type }}" @selected($account->type === $type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
                        <div class="col-md-3 form-group"><label>Status *</label><select name="status" class="form-control" required><option value="active" @selected($account->status === 'active')>Active</option><option value="inactive" @selected($account->status === 'inactive')>Inactive</option></select></div>
                        <div class="col-md-6 form-group"><label>Bank Name</label><input name="bank_name" class="form-control" value="{{ $account->bank_name }}"></div>
                        <div class="col-md-6 form-group"><label>Account Number</label><input name="account_number" class="form-control" value="{{ $account->account_number }}"></div>
                        <div class="col-md-4 form-group"><label>Opening Balance *</label><input name="opening_balance" type="number" step="0.01" min="0" class="form-control" required value="{{ $account->opening_balance }}"></div>
                        <div class="col-md-4 form-group"><label>Current Balance *</label><input name="current_balance" type="number" step="0.01" min="0" class="form-control" required value="{{ $account->current_balance }}"></div>
                        <div class="col-md-4 form-group"><label>Opening Date</label><input name="opening_balance_date" type="date" class="form-control" value="{{ $account->opening_balance_date?->toDateString() }}"></div>
                        <div class="col-12 form-group"><label>Notes</label><textarea name="notes" rows="2" class="form-control">{{ $account->notes }}</textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button class="btn btn-primary">Update</button></div>
            </form>
        </div>
    </div>
    <div class="modal fade fin-modal" id="deleteBank{{ $account->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('admin.finance.bank-accounts.destroy', $account) }}">
                @csrf @method('DELETE')
                <div class="modal-header bg-danger"><h5><i class="fas fa-trash mr-2"></i>Delete Account</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <p>Delete <strong>{{ $account->name }}</strong>?</p>
                    <div class="form-check mb-2"><input class="form-check-input" type="radio" name="transaction_action" value="keep" checked id="bankKeep{{ $account->id }}"><label class="form-check-label" for="bankKeep{{ $account->id }}">Transactions rakhna hai</label></div>
                    <div class="form-check"><input class="form-check-input" type="radio" name="transaction_action" value="delete_revert" id="bankRevert{{ $account->id }}"><label class="form-check-label" for="bankRevert{{ $account->id }}">Transactions delete karke balance revert karna hai</label></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button class="btn btn-danger">Delete</button></div>
            </form>
        </div>
    </div>
    @endforeach
    @endsection

