@extends('admin.layouts.app')

@section('title', 'Site Settings')
@section('page-title', 'Site Settings')

@section('content')

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="card shadow-sm">

    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Website Configuration</h5>
        <button class="btn btn-primary btn-sm">
            <i class="fas fa-save mr-1"></i> Save Changes
        </button>
    </div>

    <div class="card-body">

        {{-- TABS --}}
        <ul class="nav nav-tabs mb-4">
            @foreach($groups as $group)
            <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#tab-{{ $group }}">
                    {{ ucfirst($group) }}
                </a>
            </li>
            @endforeach
        </ul>

        <div class="tab-content">

            @foreach($groups as $group)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $group }}">

                <div class="row">

                    @foreach($settings[$group] as $setting)

                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold">
                            {{ $setting->label }}
                        </label>

                        {{-- TEXT --}}
                        @if($setting->type === 'text')
                        <input type="text"
                               name="settings[{{ $setting->key }}]"
                               value="{{ $setting->value }}"
                               class="form-control">

                        {{-- TEXTAREA --}}
                        @elseif($setting->type === 'textarea')
                        <textarea
                            name="settings[{{ $setting->key }}]"
                            class="form-control"
                            rows="3">{{ $setting->value }}</textarea>

                        {{-- IMAGE --}}
                        @elseif($setting->type === 'image')
                        <div>
                            @if($setting->value)
                                <img src="{{ asset('storage/settings/'.$setting->value) }}"
                                     style="height:60px;margin-bottom:10px;">
                            @endif

                            <input type="file" name="files[{{ $setting->key }}]" class="form-control">
                        </div>

                        {{-- BOOLEAN --}}
                        @elseif($setting->type === 'boolean')
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="{{ $setting->key }}"
                                   name="settings[{{ $setting->key }}]"
                                   value="1"
                                   {{ $setting->value ? 'checked' : '' }}>
                            <label class="custom-control-label" for="{{ $setting->key }}">
                                Enable
                            </label>
                        </div>

                        @endif

                    </div>

                    @endforeach

                </div>

            </div>
            @endforeach

        </div>

    </div>

</div>

</form>

@endsection
