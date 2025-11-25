<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">

    {{-- NAVBAR USER MERAH PUTIH --}}
    <nav class="bg-red-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                {{-- Kiri: Logo + Nama App --}}
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="/logo.png" alt="Logo" class="h-8 w-8 rounded-full bg-white mr-2">
                        <span class="font-semibold text-lg tracking-tight">
                            {{ config('app.name') }}
                        </span>
                    </a>
                </div>

                {{-- Menu Tengah (desktop) --}}
                <div class="hidden md:flex items-center space-x-6 text-sm">
                    <a href="{{ route('dashboard') }}"
                       class="hover:text-red-100 {{ Request::routeIs('dashboard') ? 'font-semibold underline' : '' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('videos.index') }}"
                       class="hover:text-red-100 {{ Request::routeIs('videos.*') ? 'font-semibold underline' : '' }}">
                        Video YouTube
                    </a>

                    @auth
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}"
                               class="hover:text-red-100 {{ Request::is('admin/*') ? 'font-semibold underline' : '' }}">
                                Admin
                            </a>
                        @endif
                    @endauth
                </div>

                {{-- Kanan: User/Login + Hamburger --}}
                <div class="flex items-center space-x-3">
                    @auth
                        @php
                            $name   = Auth::user()->name ?? 'User';
                            $avatar = Auth::user()->avatar
                                ?? 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=DC2626&background=FEE2E2';
                        @endphp

                        <span class="hidden sm:inline text-sm">
                            {{ $name }}
                        </span>
                        <img src="{{ $avatar }}" class="w-8 h-8 rounded-full border border-white" alt="Avatar">

                        <form action="{{ route('logout') }}" method="POST" class="hidden sm:block">
                            @csrf
                            <button type="submit"
                                    class="text-xs font-semibold bg-white text-red-600 px-3 py-1 rounded-full hover:bg-red-50">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-xs font-semibold bg-white text-red-600 px-3 py-1 rounded-full hover:bg-red-50">
                            Login
                        </a>
                    @endauth

                    {{-- Tombol menu mobile --}}
                    <button class="md:hidden focus:outline-none" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- MENU MOBILE --}}
        <div id="mobile-menu" class="md:hidden hidden border-t border-red-500 bg-red-600">
            <div class="px-4 py-3 space-y-2 text-sm">
                <a href="{{ route('dashboard') }}"
                   class="block py-1 {{ Request::routeIs('dashboard') ? 'font-semibold underline' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('videos.index') }}"
                   class="block py-1 {{ Request::routeIs('videos.*') ? 'font-semibold underline' : '' }}">
                    Video YouTube
                </a>

                @auth
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="block py-1 {{ Request::is('admin/*') ? 'font-semibold underline' : '' }}">
                            Admin
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full text-left py-1 font-semibold">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block py-1 font-semibold">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- KONTEN UTAMA --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Flash message --}}
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
        @if(session('info'))
            <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                {{ session('info') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-gray-200 py-4 mt-6 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name') }} · Membantu kreator melawan komentar judi online.
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        }
    </script>

    @stack('scripts')
</body>
</html>
