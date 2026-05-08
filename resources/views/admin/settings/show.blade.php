@extends('admin.layouts.app')

@section('title', 'View Settings')
@section('page-title', 'Settings Overview')

@section('content')

<div class="card shadow-sm">

    <div class="card-header">
        <h5>All Settings (Read Only)</h5>
    </div>

    <div class="card-body">

        @foreach($settings as $group => $items)

        <h6 class="text-primary mt-3 mb-2">{{ ucfirst($group) }}</h6>

        <div class="row">
            @foreach($items as $setting)
            <div class="col-md-6 mb-2">
                <strong>{{ $setting->label }}:</strong>

                @if($setting->type === 'image' && $setting->value)
                    <br>
                    <img src="{{ asset('storage/settings/'.$setting->value) }}" height="40">
                @else
                    {{ $setting->value ?? '-' }}
                @endif
            </div>
            @endforeach
        </div>

        <hr>

        @endforeach

    </div>

</div>

@endsection
