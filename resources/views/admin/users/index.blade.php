@extends('admin.layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-users mr-2 text-primary"></i>All Users</h3>
        <div>
            @can('users.create')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus mr-1"></i> Add User
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table datatable mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr @if($u->trashed()) style="opacity:0.6;background:#fef2f2;" @endif>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $u->avatarUrl }}" class="user-avatar user-avatar-sm mr-2" alt="">
                                <div>
                                    <div style="font-weight:600;font-size:0.875rem;color:#1e293b;">{{ $u->name }}</div>
                                    <div style="font-size:0.75rem;color:#94a3b8;">{{ $u->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:0.875rem;">{{ $u->email }}</td>
                        <td>{!! $u->primaryRoleBadge !!}</td>
                        <td style="font-size:0.875rem;">{{ $u->creator?->name ?? '—' }}</td>
                        <td>
                            @if($u->trashed())
                                <span class="badge badge-danger">Deleted</span>
                            @elseif($u->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td style="font-size:0.75rem;color:#94a3b8;">
                            {{ $u->last_login_at?->diffForHumans() ?? 'Never' }}
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.show', $u) }}" class="btn btn-info btn-sm" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('users.edit')
                                @unless($u->trashed())
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.users.toggle-status', $u) }}" class="btn btn-{{ $u->is_active ? 'secondary' : 'success' }} btn-sm" title="{{ $u->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-{{ $u->is_active ? 'ban' : 'check' }}"></i>
                                </a>
                                @endunless
                                @endcan
                                @can('users.delete')
                                @if($u->trashed())
                                <a href="{{ route('admin.users.restore', $u->id) }}" class="btn btn-success btn-sm" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </a>
                                @else
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $users->links() }}
    </div>
</div>
@endsection
