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
            <table class="table mb-0 datatable">
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
                        @can('finance.expenses.show')<a href="{{ route('admin.finance.expenses.show', $expense) }}" class="btn btn-sm btn-outline-dark" title="View statement"><i class="fas fa-eye"></i></a>@endcan
                        @can('finance.expenses.edit')@if($expense->status !== 'paid')<button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editExpense{{ $expense->id }}" title="Edit"><i class="fas fa-pen"></i></button>@endif@endcan
                        @can('finance.approve')@if(in_array($expense->status, ['submitted', 'draft', 'deferred']))<form action="{{ route('admin.finance.expenses.approve', $expense) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button></form>@endif@endcan
                        @can('finance.payments.create')@if(in_array($expense->status, ['approved', 'partial']) && $expense->remaining_amount > 0)<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paymentModal{{ $expense->id }}"><i class="fas fa-money-bill-wave"></i></button>@endif@endcan
                        @can('finance.expenses.show')<a href="{{ route('admin.finance.expenses.invoice', $expense) }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="fas fa-file-invoice"></i></a>@endcan
                        @can('finance.expenses.delete')<button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteExpense{{ $expense->id }}" title="Delete"><i class="fas fa-trash"></i></button>@endcan
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
    <div class="modal fade fin-modal" id="editExpense{{ $expense->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('admin.finance.expenses.update', $expense) }}">
                @csrf @method('PUT')
                <div class="modal-header" style="background:linear-gradient(135deg,#78350f,#d97706);"><h5><i class="fas fa-pen mr-2"></i>Edit Expense</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Ledger / Employee *</label><select name="ledger_id" class="form-control ledger-amount-source" required>@foreach($ledgers as $ledger)<option value="{{ $ledger->id }}" data-amount="{{ $ledger->default_amount }}" @selected($expense->ledger_id === $ledger->id)>{{ $ledger->name }} - {{ ucfirst($ledger->type) }}</option>@endforeach</select></div>
                        <div class="col-md-6 form-group"><label>Title *</label><input name="title" class="form-control" required value="{{ $expense->title }}"></div>
                        <div class="col-md-6 form-group"><label>Vendor / Employee Name</label><input name="vendor_name" class="form-control" value="{{ $expense->vendor_name }}"></div>
                        <div class="col-md-3 form-group"><label>Priority *</label><select name="priority" class="form-control" required>@foreach(['low','normal','high','urgent'] as $priority)<option value="{{ $priority }}" @selected($expense->priority === $priority)>{{ ucfirst($priority) }}</option>@endforeach</select></div>
                        <div class="col-md-3 form-group"><label>Status *</label><select name="status" class="form-control" required>@foreach(['draft','submitted','approved','partial','deferred','rejected','cancelled'] as $status)<option value="{{ $status }}" @selected($expense->status === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                        <div class="col-md-4 form-group"><label>Base Amount *</label><input name="planned_amount" type="number" min="1" step="0.01" class="form-control planned-amount calc-net" required value="{{ $expense->planned_amount }}"></div>
                        <div class="col-md-4 form-group"><label>Tax / GST</label><input name="tax_amount" type="number" min="0" step="0.01" class="form-control calc-net" value="{{ $expense->tax_amount }}"></div>
                        <div class="col-md-4 form-group"><label>Discount</label><input name="discount_amount" type="number" min="0" step="0.01" class="form-control calc-net" value="{{ $expense->discount_amount }}"></div>
                        <div class="col-md-4 form-group"><label>Expense Date</label><input name="expense_month" type="date" class="form-control" value="{{ $expense->expense_month ? \Illuminate\Support\Carbon::parse($expense->expense_month)->toDateString() : '' }}"></div>
                        <div class="col-md-4 form-group"><label>Due Date</label><input name="due_date" type="date" class="form-control" value="{{ $expense->due_date?->toDateString() }}"></div>
                        <div class="col-md-4 form-group"><label>Preferred Bank</label><select name="bank_account_id" class="form-control"><option value="">Decide while paying</option>@foreach($bankAccounts as $account)<option value="{{ $account->id }}" @selected($expense->bank_account_id === $account->id)>{{ $account->name }} - {{ $money($account->current_balance) }}</option>@endforeach</select></div>
                        <div class="col-12 form-group"><label>Notes</label><textarea name="notes" rows="2" class="form-control">{{ $expense->notes }}</textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button class="btn btn-warning text-white">Update</button></div>
            </form>
        </div>
    </div>
    <div class="modal fade fin-modal" id="deleteExpense{{ $expense->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('admin.finance.expenses.destroy', $expense) }}">
                @csrf @method('DELETE')
                <div class="modal-header bg-danger"><h5><i class="fas fa-trash mr-2"></i>Delete Expense</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <p>Delete <strong>{{ $expense->title }}</strong>?</p>
                    <div class="form-check mb-2"><input class="form-check-input" type="radio" name="transaction_action" value="keep" checked id="expKeep{{ $expense->id }}"><label class="form-check-label" for="expKeep{{ $expense->id }}">Transactions rakhna hai</label></div>
                    <div class="form-check"><input class="form-check-input" type="radio" name="transaction_action" value="delete_revert" id="expRevert{{ $expense->id }}"><label class="form-check-label" for="expRevert{{ $expense->id }}">Payments/transactions delete karke bank balance revert karna hai</label></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button class="btn btn-danger">Delete</button></div>
            </form>
        </div>
    </div>
@endforeach
@endsection

