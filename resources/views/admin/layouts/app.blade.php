<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>

    {{-- Tailwind + FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        html { scroll-behavior: smooth; }
        .focus-red {
            @apply focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900">
<div class="flex h-screen overflow-hidden">

    {{-- ========================= --}}
    {{-- SIDEBAR DESKTOP           --}}
    {{-- ========================= --}}
    <aside class="hidden md:flex md:flex-shrink-0">
        <div class="flex flex-col w-64 bg-white border-r border-red-200 shadow-sm">

            {{-- Brand Header --}}
            <div class="flex items-center justify-center h-16 px-4 border-b border-red-200 bg-red-600 text-white">
                <span class="text-lg font-bold tracking-wide">{{ config('app.name') }}</span>
            </div>

            {{-- Navigation --}}
            <div class="flex-1 px-4 py-4">
                <nav class="space-y-2 text-sm">

                    @auth
                        @php
                            $user = Auth::user();
                            $name = $user->name ?? 'User';
                        @endphp

                        {{-- Admin --}}
                        @if($user->is_admin)

                            {{-- Dashboard --}}
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center px-4 py-2 rounded-lg transition
                               {{ Request::routeIs('admin.dashboard')
                                   ? 'bg-red-600 text-white shadow'
                                   : 'hover:bg-red-50 text-gray-700' }}">
                                <i class="fas fa-home mr-3 text-gray-600"></i>
                                Admin Dashboard
                            </a>

                            {{-- Dataset --}}
                            <a href="{{ route('admin.dataset.index') }}"
                               class="flex items-center px-4 py-2 rounded-lg transition
                               {{ Request::routeIs('admin.dataset*')
                                   ? 'bg-red-600 text-white shadow'
                                   : 'hover:bg-red-50 text-gray-700' }}">
                                <i class="fas fa-database mr-3 text-gray-600"></i>
                                Dataset
                            </a>

                            {{-- Model LSTM --}}
                            <a href="{{ route('admin.model.index') }}"
                               class="flex items-center px-4 py-2 rounded-lg transition
                               {{ Request::routeIs('admin.model*')
                                   ? 'bg-red-600 text-white shadow'
                                   : 'hover:bg-red-50 text-gray-700' }}">
                                <i class="fas fa-project-diagram mr-3 text-gray-600"></i>
                                Model LSTM
                            </a>

                        @else
                            {{-- Normal User --}}
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center px-4 py-2 rounded-lg transition
                               {{ Request::routeIs('dashboard')
                                   ? 'bg-red-600 text-white shadow'
                                   : 'hover:bg-red-50 text-gray-700' }}">
                                <i class="fas fa-home mr-3 text-gray-600"></i>
                                Dashboard
                            </a>

                            <a href="{{ route('videos.index') }}"
                               class="flex items-center px-4 py-2 rounded-lg transition
                               {{ Request::routeIs('videos.*')
                                   ? 'bg-red-600 text-white shadow'
                                   : 'hover:bg-red-50 text-gray-700' }}">
                                <i class="fas fa-video mr-3 text-gray-600"></i>
                                My Videos
                            </a>
                        @endif
                    @endauth

                </nav>
            </div>

            {{-- User Footer --}}
            @auth
                @php
                    $avatar = $user->avatar
                        ?? 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=dc2626&color=fff';
                @endphp

                <div class="p-4 border-t border-red-200 bg-red-50">
                    <div class="flex items-center">
                        <img class="w-10 h-10 rounded-full border border-red-300"
                             src="{{ $avatar }}" alt="User avatar">
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-800">{{ $name }}</p>

                            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="text-xs text-red-600 hover:text-red-800 mt-1">
                                Logout
                            </button>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            @endauth

        </div>
    </aside>

    {{-- ========================= --}}
    {{-- MAIN CONTENT WRAPPER     --}}
    {{-- ========================= --}}
    <div class="flex flex-col flex-1 overflow-hidden">

        {{-- TOPBAR --}}
        <header class="flex items-center justify-between h-16 px-4 bg-white border-b border-red-200 shadow-sm">
            <div class="flex items-center">
                <button class="md:hidden text-gray-600 focus-red" onclick="toggleMobileSidebar()">
                    <i class="fas fa-bars"></i>
                </button>

                <h1 class="ml-4 text-lg font-semibold text-gray-800">@yield('title')</h1>
            </div>

            {{-- User --}}
            @auth
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-700 hidden sm:inline">{{ $name }}</span>

                    <img class="w-8 h-8 rounded-full border border-red-300"
                         src="{{ $avatar }}" alt="User avatar">
                </div>
            @endauth
        </header>

        {{-- ========================= --}}
        {{-- MAIN PAGE CONTENT        --}}
        {{-- ========================= --}}
        <main class="flex-1 overflow-y-auto p-4 md:p-6">

            {{-- FLASH MESSAGES --}}
            @if(session('success'))
                <div class="mb-4 flex items-center bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 flex items-center bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- PAGE CONTENT --}}
            @yield('content')
        </main>
    </div>

</div>

{{-- ========================= --}}
{{-- MOBILE SIDEBAR            --}}
{{-- ========================= --}}
<div class="fixed inset-0 z-40 md:hidden hidden" id="mobile-sidebar">

    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="toggleMobileSidebar()"></div>

    <div class="relative w-72 h-full bg-white border-r border-red-200 shadow-xl">
        <div class="flex items-center justify-between h-16 px-4 bg-red-600 text-white">
            <span class="text-lg font-semibold">{{ config('app.name') }}</span>

            <button onclick="toggleMobileSidebar()" class="text-white focus-red">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4 space-y-2">
            {{-- Same menu as desktop --}}
            @auth

                @if($user->is_admin)
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center px-4 py-2 rounded-lg transition
                       {{ Request::routeIs('admin.dashboard') ? 'bg-red-600 text-white' : 'hover:bg-red-50 text-gray-700' }}">
                        <i class="fas fa-home mr-3"></i>
                        Admin Dashboard
                    </a>

                    <a href="{{ route('admin.dataset.index') }}"
                       class="flex items-center px-4 py-2 rounded-lg transition
                       {{ Request::routeIs('admin.dataset*') ? 'bg-red-600 text-white' : 'hover:bg-red-50 text-gray-700' }}">
                        <i class="fas fa-database mr-3"></i>
                        Dataset
                    </a>

                    <a href="{{ route('admin.model.index') }}"
                       class="flex items-center px-4 py-2 rounded-lg transition
                       {{ Request::routeIs('admin.model*') ? 'bg-red-600 text-white' : 'hover:bg-red-50 text-gray-700' }}">
                        <i class="fas fa-project-diagram mr-3"></i>
                        Model LSTM
                    </a>
                @endif

            @endauth
        </div>

        {{-- Mobile User Footer --}}
        @auth
            <div class="p-4 border-t border-red-200 bg-red-50">
                <div class="flex items-center">
                    <img class="w-10 h-10 rounded-full border border-red-300"
                         src="{{ $avatar }}" alt="User avatar">
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-800">{{ $name }}</p>

                        <button onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                                class="text-xs text-red-600 hover:text-red-800 mt-1">
                            Logout
                        </button>

                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        @endauth

    </div>

</div>

<script>
    function toggleMobileSidebar() {
        document.getElementById('mobile-sidebar').classList.toggle('hidden');
    }
</script>

@stack('scripts')
</body>
</html>
