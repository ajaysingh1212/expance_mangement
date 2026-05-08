@extends('admin.layouts.app')

@section('title', isset($role) ? 'Edit Role' : 'Create Role')
@section('page-title', isset($role) ? 'Edit Role' : 'Create Role')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">{{ isset($role) ? 'Edit' : 'Create' }}</li>
@endsection

@section('content')
<form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
    @csrf
    @if(isset($role)) @method('PUT') @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3>Role Details</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $role->name ?? '') }}" placeholder="e.g. editor" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe this role...">{{ old('description', $role->description ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Role Color <span class="text-danger">*</span></label>
                        <input type="color" name="color" class="form-control" style="height:45px;padding:4px;"
                               value="{{ old('color', $role->color ?? '#4f46e5') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Icon Class <small class="text-muted">(FontAwesome)</small></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="icon-preview">
                                    <i class="{{ old('icon', $role->icon ?? 'fas fa-shield-alt') }}"></i>
                                </span>
                            </div>
                            <input type="text" name="icon" id="icon-input" class="form-control"
                                   value="{{ old('icon', $role->icon ?? 'fas fa-shield-alt') }}"
                                   placeholder="fas fa-shield-alt">
                        </div>
                        <small class="text-muted">Use any Font Awesome class</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> {{ isset($role) ? 'Update Role' : 'Create Role' }}
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-block mt-2">Cancel</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Assign Permissions</h3>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success" id="select-all">Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all">Deselect All</button>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($permissions as $module => $modulePerms)
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div style="height:2px;flex:1;background:linear-gradient(to right, #4f46e5, transparent);"></div>
                            <span style="padding:4px 16px;background:#f1f5f9;border-radius:20px;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#4f46e5;margin:0 8px;">
                                {{ $module ?? 'General' }}
                            </span>
                            <div style="height:2px;flex:1;background:linear-gradient(to left, #4f46e5, transparent);"></div>
                        </div>
                        <div class="row">
                            @foreach($modulePerms as $permission)
                            <div class="col-md-6 col-lg-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input perm-checkbox"
                                           id="perm_{{ $permission->id }}"
                                           name="permissions[]"
                                           value="{{ $permission->id }}"
                                           {{ (isset($role) && $role->permissions->contains('id', $permission->id)) || in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="perm_{{ $permission->id }}" style="font-size:0.875rem;">
                                        <span class="badge badge-{{ match($permission->group) {'index'=>'info','create'=>'success','edit'=>'warning','delete'=>'danger',default=>'secondary'} }} mr-1" style="font-size:0.65rem;">{{ $permission->group }}</span>
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Live icon preview
    document.getElementById('icon-input').addEventListener('input', function() {
        document.querySelector('#icon-preview i').className = this.value;
    });

    // Select / Deselect all
    document.getElementById('select-all').addEventListener('click', () => {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
    });
    document.getElementById('deselect-all').addEventListener('click', () => {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
    });
</script>
@endpush
@endsection
