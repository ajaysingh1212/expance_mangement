@extends('admin.layouts.app')

@section('title', 'Items')
@section('page-title', 'Items')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Items</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-boxes mr-2 text-info"></i>All Items</h3>
        @can('items.create')
        <a href="{{ route('admin.items.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Add Item
        </a>
        @endcan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table datatable mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Shared</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div style="width:40px;height:40px;border-radius:8px;overflow:hidden;background:#f1f5f9;margin-right:10px;flex-shrink:0;">
                                    @if($item->image)
                                    <img src="{{ $item->imageUrl }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                                    @else
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#94a3b8;"><i class="fas fa-box"></i></div>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:0.875rem;color:#1e293b;">{{ Str::limit($item->title, 35) }}</div>
                                    <div style="font-size:0.75rem;color:#94a3b8;">{{ Str::limit($item->description, 40) }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:0.875rem;">{{ $item->category ?? '—' }}</td>
                        <td style="font-size:0.875rem;">{{ $item->price ? '₹'.number_format($item->price, 2) : '—' }}</td>
                        <td>{!! $item->statusBadge !!}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $item->creator?->avatarUrl }}" class="user-avatar user-avatar-sm mr-1" alt="">
                                <span style="font-size:0.8rem;">{{ $item->creator?->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td>
                            @if($item->share_with_creator_admin)
                            <span class="badge badge-success"><i class="fas fa-share-alt"></i> Yes</span>
                            @else
                            <span class="badge badge-light">No</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.items.show', $item) }}" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                @can('items.edit')
                                <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('items.delete')
                                <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">{{ $items->links() }}</div>
</div>
@endsection
