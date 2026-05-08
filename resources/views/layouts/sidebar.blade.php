<div class="bg-gray-800 text-white min-h-screen p-4">
    <h2 class="text-lg font-bold mb-4">Admin Panel</h2>

    <ul>
        <li class="mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-300">Dashboard</a>
        </li>

        <li class="mb-2">
            <a href="{{ route('admin.users.index') }}" class="hover:text-gray-300">Users</a>
        </li>

        <li class="mb-2">
            <a href="{{ route('admin.roles.index') }}" class="hover:text-gray-300">Roles</a>
        </li>

        <li class="mb-2">
            <a href="{{ route('admin.permissions.index') }}" class="hover:text-gray-300">Permissions</a>
        </li>
    </ul>
</div>
