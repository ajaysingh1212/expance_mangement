@extends('admin.layouts.app')

@section('title', 'Roles')
@section('page-title', 'Role Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Roles</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-shield-alt mr-2 text-primary"></i>All Roles</h3>
        @can('roles.create')
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> New Role
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($roles as $role)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 h-100" style="border-left: 4px solid {{ $role->color }} !important;border-left-style:solid !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div style="width:44px;height:44px;border-radius:10px;background:{{ $role->color }}22;display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                                <i class="{{ $role->icon ?? 'fas fa-shield-alt' }}" style="color:{{ $role->color }};font-size:18px;"></i>
                            </div>
                            <div>
                                <h5 style="margin:0;font-weight:700;color:#1e293b;font-family:'Poppins',sans-serif;">{{ $role->name }}</h5>
                                <span class="badge" style="background:{{ $role->color }};color:#fff;font-size:0.7rem;">{{ $role->users_count }} user{{ $role->users_count != 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        @if($role->description)
                        <p style="font-size:0.8rem;color:#64748b;margin-bottom:12px;">{{ $role->description }}</p>
                        @endif
                        <div class="mb-3">
                            <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;margin-bottom:6px;">Permissions ({{ $role->permissions->count() }})</div>
                            @foreach($role->permissions->groupBy('module') as $module => $perms)
                            <span class="badge badge-light mr-1 mb-1" style="font-size:0.7rem;">{{ $module }}: {{ $perms->pluck('group')->join(', ') }}</span>
                            @endforeach
                            @if($role->permissions->isEmpty())
                            <span style="font-size:0.8rem;color:#94a3b8;font-style:italic;">No permissions assigned</span>
                            @endif
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('roles.edit')
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            @can('roles.delete')
                            @unless(in_array($role->name, ['super-admin']))
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-delete"><i class="fas fa-trash"></i></button>
                            </form>
                            @endunless
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">No roles found.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
