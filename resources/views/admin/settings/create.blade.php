@extends('admin.layouts.app')

@section('title', 'Create Setting')
@section('page-title', 'Create Setting')

@section('content')

<div class="card shadow-sm">
    <div class="card-header">
        <h5>Create New Setting</h5>
    </div>

    <form action="{{ route('admin.settings.store') }}" method="POST">
        @csrf

        <div class="card-body">

            <div class="form-group">
                <label>Key</label>
                <input type="text" name="key" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Value</label>
                <input type="text" name="value" class="form-control">
            </div>

            <div class="form-group">
                <label>Group</label>
                <select name="group" class="form-control">
                    <option value="general">General</option>
                    <option value="contact">Contact</option>
                    <option value="social">Social</option>
                    <option value="seo">SEO</option>
                    <option value="system">System</option>
                </select>
            </div>

        </div>

        <div class="card-footer text-right">
            <button class="btn btn-success">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </form>

</div>

@endsection
