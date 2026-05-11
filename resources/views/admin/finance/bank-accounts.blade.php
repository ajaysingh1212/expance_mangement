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
    @endsection
