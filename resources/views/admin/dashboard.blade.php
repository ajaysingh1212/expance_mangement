@extends('layouts.admin')

@section('title','Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- USERS --}}
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-6 rounded-2xl shadow-lg hover:scale-105 transition">
        <div class="flex justify-between items-center">
            <div>
                <h5 class="text-sm uppercase">Total Users</h5>
                <h2 class="text-3xl font-bold mt-2">{{ $users }}</h2>
            </div>
            <div class="text-4xl opacity-80">
                👤
            </div>
        </div>
    </div>

    {{-- ROLES --}}
    <div class="bg-gradient-to-r from-green-500 to-green-700 text-white p-6 rounded-2xl shadow-lg hover:scale-105 transition">
        <div class="flex justify-between items-center">
            <div>
                <h5 class="text-sm uppercase">Total Roles</h5>
                <h2 class="text-3xl font-bold mt-2">{{ $roles }}</h2>
            </div>
            <div class="text-4xl opacity-80">
                🛡️
            </div>
        </div>
    </div>

    {{-- PERMISSIONS --}}
    <div class="bg-gradient-to-r from-purple-500 to-purple-700 text-white p-6 rounded-2xl shadow-lg hover:scale-105 transition">
        <div class="flex justify-between items-center">
            <div>
                <h5 class="text-sm uppercase">Total Permissions</h5>
                <h2 class="text-3xl font-bold mt-2">{{ $permissions }}</h2>
            </div>
            <div class="text-4xl opacity-80">
                🔐
            </div>
        </div>
    </div>

</div>

{{-- EXTRA SECTION --}}
<div class="mt-8 bg-white rounded-2xl shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Welcome 👋</h3>
    <p class="text-gray-600">
        This is your RBAC multi-tenant dashboard. You can manage users, roles and permissions from sidebar.
    </p>
</div>

@endsection
