@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

{{-- Welcome Header --}}
<div class="page-header-card mb-4" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #2563eb 100%); position:relative; overflow:hidden;">
    <div style="position:absolute;top:-20px;right:-20px;width:150px;height:150px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-30px;right:80px;width:100px;height:100px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
    <div class="row align-items-center position-relative">
        <div class="col-md-8">
            <h1 style="font-family:'Poppins',sans-serif;font-size:1.6rem;font-weight:700;color:#fff;margin:0;">
                👋 Welcome back, {{ auth()->user()->name }}!
            </h1>
            <p style="color:rgba(255,255,255,0.8);margin:6px 0 0;font-size:0.9rem;">
                {{ now()->format('l, d F Y') }} &nbsp;·&nbsp;
                <span style="background:rgba(255,255,255,0.15);padding:2px 10px;border-radius:20px;font-size:0.8rem;">
                    {!! auth()->user()->primaryRoleBadge !!}
                </span>
            </p>
        </div>
        <div class="col-md-4 text-right d-none d-md-block">
            <img src="{{ auth()->user()->avatarUrl }}" style="width:70px;height:70px;border-radius:50%;border:3px solid rgba(255,255,255,0.3);object-fit:cover;" alt="">
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row">
    @if(isset($stats['total_users']))
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="info-box" style="background:#fff;">
            <span class="info-box-icon" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;">
                <i class="fas fa-users"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Users</span>
                <span class="info-box-number">{{ $stats['total_users'] }}</span>
                <div style="font-size:0.75rem;color:#10b981;margin-top:2px;">
                    <i class="fas fa-circle" style="font-size:8px;"></i> {{ $stats['active_users'] ?? 0 }} active
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($stats['total_items']))
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="info-box" style="background:#fff;">
            <span class="info-box-icon" style="background:linear-gradient(135deg,#0891b2,#06b6d4);color:#fff;">
                <i class="fas fa-boxes"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Total Items</span>
                <span class="info-box-number">{{ $stats['total_items'] }}</span>
            </div>
        </div>
    </div>
    @endif

    @if(isset($stats['total_roles']))
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="info-box" style="background:#fff;">
            <span class="info-box-icon" style="background:linear-gradient(135deg,#059669,#10b981);color:#fff;">
                <i class="fas fa-shield-alt"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Roles</span>
                <span class="info-box-number">{{ $stats['total_roles'] }}</span>
            </div>
        </div>
    </div>
    @endif

    @if(isset($stats['active_items']))
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="info-box" style="background:#fff;">
            <span class="info-box-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);color:#fff;">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Active Items</span>
                <span class="info-box-number">{{ $stats['active_items'] }}</span>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    {{-- Recent Activity --}}
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-history mr-2 text-primary"></i>Recent Activity</h3>
                @can('activity.index')
                <a href="{{ route('admin.activity.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                @endcan
            </div>
            <div class="card-body p-0">
                @forelse($recentActivity as $log)
                <div class="d-flex align-items-start px-4 py-3" style="border-bottom:1px solid #f1f5f9;">
                    <div style="width:36px;height:36px;border-radius:50%;background:{{ match($log->action) {'created'=>'#d1fae5','updated'=>'#dbeafe','deleted'=>'#fee2e2','login'=>'#ede9fe',default=>'#f1f5f9'} }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                        <i class="fas fa-{{ match($log->action) {'created'=>'plus','updated'=>'edit','deleted'=>'trash','login'=>'sign-in-alt','logout'=>'sign-out-alt',default=>'circle'} }}" style="font-size:13px;color:{{ match($log->action) {'created'=>'#059669','updated'=>'#2563eb','deleted'=>'#ef4444','login'=>'#7c3aed',default=>'#64748b'} }};"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size:0.875rem;font-weight:500;color:#1e293b;">{{ $log->description }}</div>
                        <div style="font-size:0.75rem;color:#94a3b8;">
                            {{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}
                        </div>
                    </div>
                    {!! $log->actionBadge !!}
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-history" style="font-size:2.5rem;opacity:0.2;display:block;margin-bottom:10px;"></i>
                    No recent activity
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Items --}}
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-boxes mr-2 text-info"></i>Recent Items</h3>
                @can('items.index')
                <a href="{{ route('admin.items.index') }}" class="btn btn-sm btn-outline-info">View All</a>
                @endcan
            </div>
            <div class="card-body p-0">
                @forelse($recentItems as $item)
                <div class="d-flex align-items-center px-4 py-3" style="border-bottom:1px solid #f1f5f9;">
                    <div style="width:42px;height:42px;border-radius:8px;background:#f1f5f9;overflow:hidden;flex-shrink:0;margin-right:12px;">
                        @if($item->image)
                            <img src="{{ $item->imageUrl }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#94a3b8;">
                                <i class="fas fa-box"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size:0.875rem;font-weight:500;color:#1e293b;">{{ Str::limit($item->title, 30) }}</div>
                        <div style="font-size:0.75rem;color:#94a3b8;">by {{ $item->creator?->name }}</div>
                    </div>
                    {!! $item->statusBadge !!}
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-box-open" style="font-size:2.5rem;opacity:0.2;display:block;margin-bottom:10px;"></i>
                    No items yet
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
