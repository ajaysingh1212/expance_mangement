@extends('admin.layouts.app')

@section('title', isset($permission) ? 'Edit Permission' : 'Create Permission')
@section('page-title', isset($permission) ? 'Edit Permission' : 'Create Permission')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
    <li class="breadcrumb-item active">{{ isset($permission) ? 'Edit' : 'Create' }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-key mr-2 text-warning"></i>{{ isset($permission) ? 'Edit Permission' : 'Create Permission' }}</h3>
            </div>
            <form action="{{ isset($permission) ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}" method="POST">
                @csrf
                @if(isset($permission)) @method('PUT') @endif
                <div class="card-body">
                    <div class="form-group">
                        <label>Permission Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $permission->name ?? '') }}"
                               placeholder="e.g. items.create" required>
                        <small class="text-muted">Format: module.action (e.g. users.index, items.create)</small>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Module <span class="text-danger">*</span></label>
                        <input type="text" name="module" class="form-control @error('module') is-invalid @enderror"
                               value="{{ old('module', $permission->module ?? '') }}"
                               list="modules-list" placeholder="e.g. items" required>
                        <datalist id="modules-list">
                            @foreach($modules as $m)
                            <option value="{{ $m }}">
                            @endforeach
                        </datalist>
                        @error('module')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Group <span class="text-danger">*</span></label>
                        <select name="group" class="form-control @error('group') is-invalid @enderror" required>
                            @foreach(['index','create','edit','delete','show'] as $g)
                            <option value="{{ $g }}" {{ old('group', $permission->group ?? '') === $g ? 'selected' : '' }}>
                                {{ ucfirst($g) }}
                            </option>
                            @endforeach
                        </select>
                        @error('group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control"
                               value="{{ old('description', $permission->description ?? '') }}"
                               placeholder="What this permission allows...">
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> {{ isset($permission) ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
