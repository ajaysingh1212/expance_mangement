@extends('admin.layouts.app')

@section('title', 'Role Details')
@section('page-title', 'Role Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">{{ $role->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div style="width:76px;height:76px;border-radius:16px;background:{{ $role->color }}22;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i class="{{ $role->icon ?? 'fas fa-shield-alt' }}" style="font-size:30px;color:{{ $role->color }}"></i>
                </div>
                <h3 style="font-size:1.2rem;font-weight:700;color:#111827;">{{ $role->name }}</h3>
                <p class="text-muted mb-3">{{ $role->description ?: 'No description added.' }}</p>
                <span class="badge" style="background:{{ $role->color }};color:#fff;">{{ $role->permissions->count() }} permissions</span>
                <span class="badge badge-light">{{ $role->users->count() }} users</span>
                <div class="mt-4">
                    @can('roles.edit')
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit Role
                    </a>
                    @endcan
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-key mr-2 text-primary"></i>Permission Matrix</h3></div>
            <div class="card-body">
                @forelse($role->permissions->groupBy('module') as $module => $permissions)
                    <div class="mb-3 pb-3" style="border-bottom:1px solid #eef2f7;">
                        <div class="font-weight-bold text-uppercase mb-2" style="font-size:0.75rem;color:#64748b;">{{ $module ?: 'General' }}</div>
                        @foreach($permissions as $permission)
                            <span class="badge badge-light mr-1 mb-1" style="border:1px solid #e5e7eb;">{{ $permission->name }}</span>
                        @endforeach
                    </div>
                @empty
                    <div class="text-muted text-center py-4">No permissions assigned.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
