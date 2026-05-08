@extends('admin.layouts.app')

@section('title', 'Create Role')
@section('page-title', 'Create Role')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3>Role Details</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. finance-manager" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe this role...">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Role Color <span class="text-danger">*</span></label>
                        <input type="color" name="color" class="form-control" style="height:45px;padding:4px;" value="{{ old('color', '#0f766e') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Icon Class <small class="text-muted">(FontAwesome)</small></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" id="icon-preview"><i class="{{ old('icon', 'fas fa-shield-alt') }}"></i></span></div>
                            <input type="text" name="icon" id="icon-input" class="form-control" value="{{ old('icon', 'fas fa-shield-alt') }}" placeholder="fas fa-shield-alt">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save mr-1"></i> Create Role</button>
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
                        <div class="font-weight-bold text-uppercase mb-2" style="font-size:0.75rem;color:#64748b;">{{ $module ?? 'General' }}</div>
                        <div class="row">
                            @foreach($modulePerms as $permission)
                            <div class="col-md-6 col-lg-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input perm-checkbox" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="perm_{{ $permission->id }}" style="font-size:0.875rem;">
                                        <span class="badge badge-light mr-1">{{ $permission->group }}</span>{{ $permission->name }}
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
document.getElementById('icon-input').addEventListener('input', function() {
    document.querySelector('#icon-preview i').className = this.value;
});
document.getElementById('select-all').addEventListener('click', () => document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true));
document.getElementById('deselect-all').addEventListener('click', () => document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false));
</script>
@endpush
@endsection
