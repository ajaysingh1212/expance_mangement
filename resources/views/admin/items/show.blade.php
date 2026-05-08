@extends('admin.layouts.app')

@section('title', $item->title)
@section('page-title', 'Item Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.items.index') }}">Items</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($item->title, 30) }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body p-0">
                @if($item->image)
                <img src="{{ $item->imageUrl }}" style="width:100%;border-radius:12px 12px 0 0;max-height:250px;object-fit:cover;" alt="">
                @else
                <div style="height:200px;background:linear-gradient(135deg,#f1f5f9,#e2e8f0);display:flex;align-items:center;justify-content:center;border-radius:12px 12px 0 0;">
                    <i class="fas fa-box-open" style="font-size:4rem;color:#cbd5e1;"></i>
                </div>
                @endif
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        {!! $item->statusBadge !!}
                        @if($item->share_with_creator_admin)
                        <span class="badge badge-info"><i class="fas fa-share-alt mr-1"></i>Shared</span>
                        @endif
                    </div>
                    @if($item->price)
                    <div style="font-size:1.5rem;font-weight:700;color:#4f46e5;margin-bottom:8px;">₹{{ number_format($item->price, 2) }}</div>
                    @endif
                    @if($item->category)
                    <div><span class="badge badge-light"><i class="fas fa-tag mr-1"></i>{{ $item->category }}</span></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>{{ $item->title }}</h3>
                <div>
                    @can('items.edit')
                    <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit mr-1"></i>Edit</a>
                    @endcan
                    @can('items.delete')
                    <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash mr-1"></i>Delete</button>
                    </form>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                @if($item->description)
                <div style="color:#475569;line-height:1.8;font-size:0.9rem;">{{ $item->description }}</div>
                @else
                <p class="text-muted">No description provided.</p>
                @endif

                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;">Created By</div>
                        <div class="d-flex align-items-center mt-1">
                            <img src="{{ $item->creator?->avatarUrl }}" class="user-avatar user-avatar-sm mr-2" alt="">
                            <span style="font-weight:500;font-size:0.875rem;">{{ $item->creator?->name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;">Created On</div>
                        <div style="font-weight:500;font-size:0.875rem;margin-top:4px;">{{ $item->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.items.index') }}" class="btn btn-secondary btn-sm mt-2">
            <i class="fas fa-arrow-left mr-1"></i> Back to Items
        </a>
    </div>
</div>
@endsection
