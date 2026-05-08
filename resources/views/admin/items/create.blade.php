@extends('admin.layouts.app')

@section('title', isset($item) ? 'Edit Item' : 'Create Item')
@section('page-title', isset($item) ? 'Edit Item' : 'Create Item')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.items.index') }}">Items</a></li>
    <li class="breadcrumb-item active">{{ isset($item) ? 'Edit' : 'Create' }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-{{ isset($item) ? 'edit' : 'plus' }} mr-2 text-{{ isset($item) ? 'warning' : 'primary' }}"></i>{{ isset($item) ? 'Edit Item' : 'Add New Item' }}</h3>
    </div>
    <form action="{{ isset($item) ? route('admin.items.update', $item) : route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($item)) @method('PUT') @endif
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $item->title ?? '') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="5">{{ old('description', $item->description ?? '') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category</label>
                                <input type="text" name="category" class="form-control"
                                       value="{{ old('category', $item->category ?? '') }}" placeholder="e.g. Electronics">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Price (₹)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                    <input type="number" name="price" class="form-control" step="0.01" min="0"
                                           value="{{ old('price', $item->price ?? '') }}" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            @foreach(['active','inactive','draft'] as $s)
                            <option value="{{ $s }}" {{ old('status', $item->status ?? 'active') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        @if(isset($item) && $item->image)
                        <div class="mb-2">
                            <img src="{{ $item->imageUrl }}" style="width:100%;border-radius:8px;max-height:150px;object-fit:cover;" alt="">
                        </div>
                        @endif
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                            <label class="custom-file-label" for="image">Choose image...</label>
                        </div>
                        <small class="text-muted">JPG, PNG, WebP. Max 2MB</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="share" name="share_with_creator_admin" value="1"
                                   {{ old('share_with_creator_admin', $item->share_with_creator_admin ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="share">
                                <i class="fas fa-share-alt mr-1 text-info"></i>
                                Share with my Admin
                            </label>
                        </div>
                        <small class="text-muted" style="font-size:0.75rem;">Allow the admin who created your account to see this item</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-{{ isset($item) ? 'warning' : 'primary' }}">
                <i class="fas fa-save mr-1"></i> {{ isset($item) ? 'Update Item' : 'Create Item' }}
            </button>
        </div>
    </form>
</div>
@endsection
