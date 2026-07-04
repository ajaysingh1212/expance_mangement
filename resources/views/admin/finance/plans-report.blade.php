@extends('admin.layouts.app')

@section('title', 'Finance Plan Report')
@section('page-title', 'Finance Plan Report')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Finance Plan Report</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-filter mr-2 text-primary"></i>Expense & Cashflow Filters</h3>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-left mr-1"></i> Dashboard</a>
    </div>
    <div class="card-body">
        <form id="planReportFilter" class="row">
            <div class="col-md-2 mb-2">
                <select name="type" class="form-control">
                    <option value="all">All Types</option>
                    <option value="expense">Expense</option>
                    <option value="cashflow">Cash In</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="role" class="form-control">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)<option value="{{ $role }}">{{ $role }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="user_id" class="form-control">
                    <option value="">All Users</option>
                    @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="ledger_id" class="form-control">
                    <option value="">All Ledgers</option>
                    @foreach($ledgers as $ledger)<option value="{{ $ledger->id }}">{{ $ledger->name }} ({{ $ledger->type }})</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2"><input type="date" name="from" class="form-control"></div>
            <div class="col-md-2 mb-2"><input type="date" name="to" class="form-control"></div>
            <div class="col-md-2 mb-2">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    @foreach(['draft','submitted','approved','partial','paid','received','deferred','rejected','cancelled'] as $status)
                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Results <span id="reportCount" class="badge badge-primary">{{ $rows->count() }}</span></h3></div>
    <div class="card-body table-responsive" id="reportTable">
        @include('admin.finance.partials.plans-report-table', ['rows' => $rows])
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#planReportFilter').on('submit change', function (event) {
    event.preventDefault();
    $.get('{{ route('admin.finance.plans.report') }}', $(this).serialize(), function (response) {
        $('#reportTable').html(response.html);
        $('#reportCount').text(response.count);
        $('#reportTable .datatable').DataTable({ destroy: true, pageLength: 15 });
    });
});
</script>
@endpush
