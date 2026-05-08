@extends('admin.layouts.app')

@section('title', 'Edit Setting')
@section('page-title', 'Edit Setting')

@section('content')

<div class="card shadow-sm">
    <div class="card-header">
        <h5>Edit Setting</h5>
    </div>

    <form action="{{ route('admin.settings.update', $setting->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="form-group">
                <label>Key</label>
                <input type="text" name="key" value="{{ $setting->key }}" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label>Value</label>
                <input type="text" name="value" value="{{ $setting->value }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Group</label>
                <select name="group" class="form-control">
                    @foreach(['general','contact','social','seo','system'] as $group)
                        <option value="{{ $group }}" {{ $setting->group == $group ? 'selected' : '' }}>
                            {{ ucfirst($group) }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="card-footer text-right">
            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
        </div>
    </form>

</div>

@endsection
