<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="bg-white shadow rounded-lg p-4 mb-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">
                    @yield('title')
                </h2>

                <a href="{{ route('admin.dashboard') }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Dashboard
                </a>
            </div>

            {{-- CONTENT --}}
            <div class="bg-white shadow rounded-lg p-6">
                @yield('content')
            </div>

        </div>
    </div>

    @yield('scripts')
</x-app-layout>
