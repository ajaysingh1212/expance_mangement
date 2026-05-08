@extends('admin.layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="card mb-4" style="overflow:hidden;">
    {{-- Cover Photo --}}
    <div style="height:220px;background:linear-gradient(135deg,#4f46e5,#7c3aed,#2563eb);position:relative;overflow:hidden;">
        @if($user->cover_photo && $user->cover_photo !== 'default-cover.jpg')

        <img src="{{ $user->coverPhotoUrl }}" style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;" alt="">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.3);"></div>
        @else
        <div style="position:absolute;inset:0;background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 50%,#2563eb 100%);opacity:0.9;"></div>
        <div style="position:absolute;top:20px;left:30px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
        <div style="position:absolute;bottom:-40px;right:100px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
        @endif

        {{-- Change cover button --}}
        <label for="cover-upload" style="position:absolute;bottom:12px;right:16px;cursor:pointer;">
            <span class="btn btn-sm" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.4);backdrop-filter:blur(10px);">
                <i class="fas fa-camera mr-1"></i> Change Cover
            </span>
        </label>
        <form id="cover-form" action="{{ route('admin.profile.cover') }}" method="POST" enctype="multipart/form-data" style="display:none;">
            @csrf
            <input type="file" id="cover-upload" name="cover_photo" accept="image/*" onchange="this.form.submit()">
        </form>
    </div>

    {{-- Avatar & basic info --}}
    <div class="card-body" style="position:relative;padding-top:0;">
        <div style="display:flex;align-items:flex-end;margin-top:-55px;margin-bottom:16px;flex-wrap:wrap;gap:16px;">
            <div style="position:relative;display:inline-block;">
                <img src="{{ $user->avatarUrl }}" style="width:110px;height:110px;border-radius:50%;border:4px solid #fff;object-fit:cover;box-shadow:0 4px 20px rgba(0,0,0,0.15);" alt="">
                <label for="avatar-upload" style="position:absolute;bottom:4px;right:4px;width:30px;height:30px;background:#4f46e5;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid #fff;">
                    <i class="fas fa-camera text-white" style="font-size:12px;"></i>
                </label>
                <form id="avatar-form" action="{{ route('admin.profile.avatar') }}" method="POST" enctype="multipart/form-data" style="display:none;">
                    @csrf
                    <input type="file" id="avatar-upload" name="avatar" accept="image/*" onchange="this.form.submit()">
                </form>
            </div>
            <div style="padding-bottom:8px;">
                <h3 style="font-family:'Poppins',sans-serif;font-weight:700;color:#1e293b;margin:0;">{{ $user->name }}</h3>
                <div style="color:#64748b;font-size:0.875rem;">{{ $user->username }}</div>
                <div class="mt-1 d-flex gap-2 flex-wrap">
                    {!! $user->primaryRoleBadge !!}
                    @if($user->designation)
                    <span class="badge badge-light">{{ $user->designation }}</span>
                    @endif
                    @if($user->department)
                    <span class="badge badge-light">{{ $user->department }}</span>
                    @endif
                </div>
            </div>
            <div class="ml-auto d-flex gap-2 pb-2">
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit mr-1"></i> Edit Profile
                </a>
            </div>
        </div>

        @if($user->bio)
        <p style="color:#475569;font-size:0.9rem;line-height:1.8;max-width:700px;">{{ $user->bio }}</p>
        @endif

        {{-- Stats row --}}
        <div class="row mt-3" style="border-top:1px solid #f1f5f9;padding-top:16px;">
            <div class="col-4 col-md-2 text-center">
                <div style="font-size:1.4rem;font-weight:700;color:#4f46e5;">{{ $user->items->count() }}</div>
                <div style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Items</div>
            </div>
            <div class="col-4 col-md-2 text-center">
                <div style="font-size:1.4rem;font-weight:700;color:#10b981;">{{ $user->createdUsers->count() }}</div>
                <div style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Users Created</div>
            </div>
            @if($user->age)
            <div class="col-4 col-md-2 text-center">
                <div style="font-size:1.4rem;font-weight:700;color:#f59e0b;">{{ $user->age }}</div>
                <div style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Years Old</div>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    {{-- Personal Info --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header"><h3><i class="fas fa-user mr-2 text-primary"></i>Personal Info</h3></div>
            <div class="card-body">
                @php
                $infos = [
                    ['icon'=>'fas fa-envelope','label'=>'Email','value'=>$user->email,'color'=>'text-primary'],
                    ['icon'=>'fas fa-phone','label'=>'Phone','value'=>$user->phone,'color'=>'text-success'],
                    ['icon'=>'fas fa-birthday-cake','label'=>'Birthday','value'=>$user->date_of_birth?->format('d M Y'),'color'=>'text-warning'],
                    ['icon'=>'fas fa-venus-mars','label'=>'Gender','value'=>$user->gender ? ucfirst($user->gender) : null,'color'=>'text-info'],
                    ['icon'=>'fas fa-map-marker-alt','label'=>'Location','value'=>$user->fullAddress,'color'=>'text-danger'],
                    ['icon'=>'fas fa-globe','label'=>'Website','value'=>$user->website,'color'=>'text-secondary'],
                ];
                @endphp
                @foreach($infos as $info)
                @if($info['value'])
                <div class="d-flex align-items-start mb-3">
                    <div style="width:36px;height:36px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                        <i class="{{ $info['icon'] }} {{ $info['color'] }}" style="font-size:14px;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;">{{ $info['label'] }}</div>
                        <div style="font-size:0.875rem;color:#1e293b;font-weight:500;">
                            @if($info['label'] === 'Website')
                            <a href="{{ $info['value'] }}" target="_blank" style="color:#4f46e5;">{{ $info['value'] }}</a>
                            @else
                            {{ $info['value'] }}
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Social Links --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header"><h3><i class="fas fa-share-alt mr-2 text-info"></i>Social Links</h3></div>
            <div class="card-body">
                @php
                $socials = [
                    ['icon'=>'fab fa-facebook-f','label'=>'Facebook','value'=>$user->facebook,'color'=>'#1877f2'],
                    ['icon'=>'fab fa-twitter','label'=>'Twitter / X','value'=>$user->twitter,'color'=>'#1da1f2'],
                    ['icon'=>'fab fa-linkedin-in','label'=>'LinkedIn','value'=>$user->linkedin,'color'=>'#0a66c2'],
                    ['icon'=>'fab fa-instagram','label'=>'Instagram','value'=>$user->instagram,'color'=>'#e4405f'],
                    ['icon'=>'fab fa-github','label'=>'GitHub','value'=>$user->github,'color'=>'#24292e'],
                ];
                @endphp
                @foreach($socials as $s)
                <div class="d-flex align-items-center mb-3">
                    <div style="width:36px;height:36px;border-radius:8px;background:{{ $s['color'] }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                        <i class="{{ $s['icon'] }}" style="color:{{ $s['color'] }};font-size:14px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">{{ $s['label'] }}</div>
                        @if($s['value'])
                        <a href="{{ $s['value'] }}" target="_blank" style="font-size:0.875rem;color:#4f46e5;word-break:break-all;">{{ $s['value'] }}</a>
                        @else
                        <div style="font-size:0.8rem;color:#cbd5e1;font-style:italic;">Not set</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-history mr-2 text-warning"></i>Recent Activity</h3></div>
            <div class="card-body p-0">
                @forelse($user->activityLogs->take(10) as $log)
                <div class="d-flex align-items-center px-4 py-3" style="border-bottom:1px solid #f1f5f9;">
                    {!! $log->actionBadge !!}
                    <span class="ml-2" style="font-size:0.875rem;color:#475569;">{{ $log->description }}</span>
                    <span class="ml-auto" style="font-size:0.75rem;color:#94a3b8;white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="text-center py-5 text-muted">No activity recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
