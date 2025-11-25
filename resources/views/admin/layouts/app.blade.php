<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
          integrity="sha512-1ycn6IcaQQ40KtH2zO/Kc/hV4zY55c3j42x54sW24+dJp7sB8F+C+lW1sE70c/5B78l4n4aC+f0d+lW6rXQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen">
    {{-- SIDEBAR DESKTOP --}}
    <div class="hidden md:flex md:flex-shrink-0">
        <div class="flex flex-col w-64 bg-indigo-800 text-white">
            <div class="flex items-center justify-center h-16 px-4 bg-indigo-900">
                <span class="text-xl font-semibold">{{ config('app.name') }}</span>
            </div>

            <div class="flex flex-col flex-grow px-4 py-4">
                <nav class="flex-1 space-y-2">
                    @auth
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ Request::routeIs('admin.dashboard') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                                <i class="fas fa-shield-alt mr-3"></i> Admin Dashboard
                            </a>

                            <a href="{{ route('admin.dataset.index') }}"
                               class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ Request::routeIs('admin.dataset*') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                                <i class="fas fa-database mr-3"></i> Dataset
                            </a>

                            <a href="{{ route('admin.model.index') }}"
                               class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ Request::routeIs('admin.model*') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                                <i class="fas fa-project-diagram mr-3"></i> Model LSTM
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ Request::routeIs('dashboard') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                                <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                            </a>

                            <a href="{{ route('videos.index') }}"
                               class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ Request::routeIs('videos.*') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                                <i class="fas fa-video mr-3"></i> My Videos
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>

            @auth
                <div class="p-4 border-t border-indigo-700">
                    <div class="flex items-center">
                        @php
                            $name = Auth::user()->name ?? 'User';
                            $avatar = Auth::user()->avatar
                                ?? 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
                        @endphp
                        <img class="w-10 h-10 rounded-full" src="{{ $avatar }}" alt="User avatar">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">{{ $name }}</p>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="text-xs text-indigo-200 hover:text-white">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="flex flex-col flex-1 overflow-hidden">
        {{-- Topbar --}}
        <div class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200">
            <div class="flex items-center">
                <button class="md:hidden text-gray-500 focus:outline-none" onclick="toggleMobileSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="ml-4 text-lg font-semibold text-gray-800">@yield('title', 'Admin')</h1>
            </div>

            <div class="flex items-center space-x-4">
                <button class="p-1 text-gray-500 rounded-full hover:text-gray-600 focus:outline-none">
                    <i class="fas fa-bell"></i>
                </button>

                @auth
                    @php
                        $name = Auth::user()->name ?? 'User';
                        $avatarSmall = Auth::user()->avatar
                            ?? 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
                    @endphp
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <span class="text-sm font-medium text-gray-700">{{ $name }}</span>
                            <img class="w-8 h-8 rounded-full" src="{{ $avatarSmall }}" alt="User avatar">
                        </button>
                    </div>
                @endauth
            </div>
        </div>

        {{-- Konten --}}
        <main class="flex-1 overflow-y-auto p-4">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Mobile Sidebar --}}
<div class="fixed inset-0 z-40 md:hidden hidden" id="mobile-sidebar">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" onclick="toggleMobileSidebar()"></div>
    <div class="relative flex flex-col w-72 max-w-xs bg-indigo-800">
        <div class="flex items-center justify-between h-16 px-4 bg-indigo-900">
            <span class="text-xl font-semibold text-white">{{ config('app.name') }}</span>
            <button onclick="toggleMobileSidebar()" class="text-indigo-200 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="flex-1 px-4 py-4 overflow-y-auto">
            <nav class="space-y-2">
                @auth
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ Request::routeIs('admin.dashboard') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                            <i class="fas fa-shield-alt mr-3"></i> Admin Dashboard
                        </a>
                        <a href="{{ route('admin.dataset.index') }}"
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ Request::routeIs('admin.dataset*') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                            <i class="fas fa-database mr-3"></i> Dataset
                        </a>
                        <a href="{{ route('admin.model.index') }}"
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ Request::routeIs('admin.model*') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                            <i class="fas a-project-diagram mr-3"></i> Model LSTM
                        </a>
                    @endif
                @endauth
            </nav>
        </div>

        @auth
            <div class="p-4 border-t border-indigo-700">
                @php
                    $name = Auth::user()->name ?? 'User';
                    $avatar = Auth::user()->avatar
                        ?? 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
                @endphp
                <div class="flex items-center">
                    <img class="w-10 h-10 rounded-full" src="{{ $avatar }}" alt="User avatar">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">{{ $name }}</p>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                           class="text-xs text-indigo-200 hover:text-white">
                            Logout
                        </a>
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
