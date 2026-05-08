@extends('admin.layouts.app')

@section('title', 'Permissions')
@section('page-title', 'Permission Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Permissions</li>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-key mr-2 text-warning"></i>All Permissions</h3>
        @can('permissions.create')
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> New Permission
        </a>
        @endcan
    </div>
    <div class="card-body">
        @foreach($permissions as $module => $modulePerms)
        <div class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <span style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;padding:6px 16px;border-radius:20px;font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="fas fa-cube mr-1"></i> {{ $module ?? 'General' }}
                </span>
                <span class="ml-2 text-muted" style="font-size:0.8rem;">{{ $modulePerms->count() }} permissions</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm" style="background:#fff;border-radius:8px;overflow:hidden;">
                    <thead>
                        <tr>
                            <th>Permission Name</th>
                            <th>Group</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modulePerms as $perm)
                        <tr>
                            <td>
                                <code style="background:#f1f5f9;padding:2px 8px;border-radius:4px;font-size:0.8rem;color:#4f46e5;">{{ $perm->name }}</code>
                            </td>
                            <td>
                                <span class="badge badge-{{ match($perm->group) {'index'=>'info','create'=>'success','edit'=>'warning','delete'=>'danger',default=>'secondary'} }}">
                                    {{ $perm->group }}
                                </span>
                            </td>
                            <td style="font-size:0.8rem;color:#64748b;">{{ $perm->description ?? '—' }}</td>
                            <td>
                                @can('permissions.edit')
                                <a href="{{ route('admin.permissions.edit', $perm) }}" class="btn btn-xs btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('permissions.delete')
                                <form action="{{ route('admin.permissions.destroy', $perm) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger btn-delete"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
