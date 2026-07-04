@extends('admin.layouts.app')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.profile.index') }}">Profile</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    {{-- Sidebar nav --}}
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body text-center pb-0">
                <img src="{{ $user->avatarUrl }}" style="width:80px;height:80px;border-radius:50%;border:3px solid #e2e8f0;object-fit:cover;" alt="">
                <h6 style="margin-top:10px;font-weight:700;color:#1e293b;">{{ $user->name }}</h6>
                <div style="font-size:0.8rem;color:#94a3b8;">{{ $user->username }}</div>
            </div>
            <div class="list-group list-group-flush mt-3" id="profile-tabs" role="tablist">
                <a class="list-group-item list-group-item-action active" data-toggle="tab" href="#tab-personal">
                    <i class="fas fa-user mr-2 text-primary"></i> Personal Info
                </a>
                <a class="list-group-item list-group-item-action" data-toggle="tab" href="#tab-social">
                    <i class="fas fa-share-alt mr-2 text-info"></i> Social Links
                </a>
                <a class="list-group-item list-group-item-action" data-toggle="tab" href="#tab-address">
                    <i class="fas fa-map-marker-alt mr-2 text-danger"></i> Address
                </a>
                <a class="list-group-item list-group-item-action" data-toggle="tab" href="#tab-password">
                    <i class="fas fa-lock mr-2 text-warning"></i> Change Password
                </a>

            </div>
        </div>
    </div>

    {{-- Tab content --}}
    <div class="col-md-9">
        <div class="tab-content">

            {{-- Personal Info Tab --}}
            <div class="tab-pane fade show active" id="tab-personal">
                <div class="card">
                    <div class="card-header"><h3><i class="fas fa-user mr-2 text-primary"></i>Personal Information</h3></div>
                    @include('admin.profile.partials.pin-setup')
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Username <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">@</span></div>
                                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                                        </div>
                                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date of Birth</label>
                                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select name="gender" class="form-control">
                                            <option value="">Select...</option>
                                            @foreach(['male','female','other'] as $g)
                                            <option value="{{ $g }}" {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Designation</label>
                                        <input type="text" name="designation" class="form-control" value="{{ old('designation', $user->designation) }}" placeholder="Software Engineer">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <input type="text" name="department" class="form-control" value="{{ old('department', $user->department) }}" placeholder="Engineering">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Bio</label>
                                        <textarea name="bio" class="form-control" rows="4" placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Profile Photo</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="avatar" id="avatar-input" accept="image/*">
                                            <label class="custom-file-label" for="avatar-input">Choose photo</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cover Photo</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="cover_photo" id="cover-input" accept="image/*">
                                            <label class="custom-file-label" for="cover-input">Choose cover</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Social Links Tab --}}
            <div class="tab-pane fade" id="tab-social">
                <div class="card">
                    <div class="card-header"><h3><i class="fas fa-share-alt mr-2 text-info"></i>Social Links</h3></div>
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <div class="card-body">
                            @foreach([
                                ['name'=>'facebook','icon'=>'fab fa-facebook-f','label'=>'Facebook URL','color'=>'#1877f2','placeholder'=>'https://facebook.com/username'],
                                ['name'=>'twitter','icon'=>'fab fa-twitter','label'=>'Twitter / X URL','color'=>'#1da1f2','placeholder'=>'https://x.com/username'],
                                ['name'=>'linkedin','icon'=>'fab fa-linkedin-in','label'=>'LinkedIn URL','color'=>'#0a66c2','placeholder'=>'https://linkedin.com/in/username'],
                                ['name'=>'instagram','icon'=>'fab fa-instagram','label'=>'Instagram URL','color'=>'#e4405f','placeholder'=>'https://instagram.com/username'],
                                ['name'=>'github','icon'=>'fab fa-github','label'=>'GitHub URL','color'=>'#24292e','placeholder'=>'https://github.com/username'],
                                ['name'=>'website','icon'=>'fas fa-globe','label'=>'Personal Website','color'=>'#4f46e5','placeholder'=>'https://yourwebsite.com'],
                            ] as $social)
                            <div class="form-group">
                                <label>{{ $social['label'] }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background:{{ $social['color'] }}18;border-color:#e2e8f0;">
                                            <i class="{{ $social['icon'] }}" style="color:{{ $social['color'] }};"></i>
                                        </span>
                                    </div>
                                    <input type="url" name="{{ $social['name'] }}" class="form-control" value="{{ old($social['name'], $user->{$social['name']}) }}" placeholder="{{ $social['placeholder'] }}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Social Links</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Address Tab --}}
            <div class="tab-pane fade" id="tab-address">
                <div class="card">
                    <div class="card-header"><h3><i class="fas fa-map-marker-alt mr-2 text-danger"></i>Address</h3></div>
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Street Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6"><div class="form-group"><label>City</label><input type="text" name="city" class="form-control" value="{{ old('city', $user->city) }}"></div></div>
                                <div class="col-md-6"><div class="form-group"><label>State</label><input type="text" name="state" class="form-control" value="{{ old('state', $user->state) }}"></div></div>
                                <div class="col-md-6"><div class="form-group"><label>Country</label><input type="text" name="country" class="form-control" value="{{ old('country', $user->country) }}"></div></div>
                                <div class="col-md-6"><div class="form-group"><label>Postal Code</label><input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $user->postal_code) }}"></div></div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Address</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Password Tab --}}
            <div class="tab-pane fade" id="tab-password">
                <div class="card">
                    <div class="card-header"><h3><i class="fas fa-lock mr-2 text-warning"></i>Change Password</h3></div>
                    <form action="{{ route('admin.profile.password') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Current Password <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="alert alert-info" style="font-size:0.85rem;">
                                <i class="fas fa-info-circle mr-1"></i>
                                Password must be at least 8 characters and different from current password.
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-key mr-1"></i> Change Password</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
