@extends('admin.layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Filter Logs</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="row">

                <div class="col-md-3">
                    <label>Action</label>
                    <select name="action" class="form-control">
                        <option value="">All</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Activity Logs</h5>

        <form action="{{ route('admin.activity.clear') }}" method="POST" onsubmit="return confirm('Clear old logs?')">
            @csrf
            <button class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i> Clear 30 Days Old
            </button>
        </form>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $log->user->name ?? 'System' }}
                            </td>

                            <td>
                                <span class="badge badge-{{
                                    match($log->action) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        default => 'secondary'
                                    }
                                }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>

                            <td>{{ $log->description }}</td>

                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No activity logs found
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>

    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
    @endif

</div>

@endsection
