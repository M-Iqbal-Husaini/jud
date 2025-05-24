<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-indigo-800 text-white">
                <div class="flex items-center justify-center h-16 px-4 bg-indigo-900">
                    <span class="text-xl font-semibold">{{ config('app.name') }}</span>
                </div>
                <div class="flex flex-col flex-grow px-4 py-4">
                    <nav class="flex-1 space-y-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-indigo-900 text-white">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-indigo-200 hover:bg-indigo-700 hover:text-white">
                            <i class="fas fa-user mr-3"></i>
                            Profile
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-indigo-200 hover:bg-indigo-700 hover:text-white">
                            <i class="fas fa-envelope mr-3"></i>
                            Messages
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-indigo-200 hover:bg-indigo-700 hover:text-white">
                            <i class="fas fa-cog mr-3"></i>
                            Settings
                        </a>
                    </nav>
                </div>
                <div class="p-4 border-t border-indigo-700">
                    <div class="flex items-center">
                        <img class="w-10 h-10 rounded-full" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF' }}" alt="User avatar">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-xs text-indigo-200 hover:text-white">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Navigation -->
            <div class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <button class="md:hidden text-gray-500 focus:outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="ml-4 text-lg font-semibold text-gray-800">@yield('title')</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="p-1 text-gray-500 rounded-full hover:text-gray-600 focus:outline-none">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                            <img class="w-8 h-8 rounded-full" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF' }}" alt="User avatar">
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div class="fixed inset-0 z-40 md:hidden hidden" id="mobile-sidebar">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        <div class="relative flex flex-col w-72 max-w-xs bg-indigo-800">
            <!-- Mobile sidebar content -->
            <div class="flex items-center justify-center h-16 px-4 bg-indigo-900">
                <span class="text-xl font-semibold text-white">{{ config('app.name') }}</span>
            </div>
            <div class="flex-1 px-4 py-4 overflow-y-auto">
                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-indigo-900 text-white">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-indigo-200 hover:bg-indigo-700 hover:text-white">
                        <i class="fas fa-user mr-3"></i>
                        Profile
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-indigo-200 hover:bg-indigo-700 hover:text-white">
                        <i class="fas fa-envelope mr-3"></i>
                        Messages
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-indigo-200 hover:bg-indigo-700 hover:text-white">
                        <i class="fas fa-cog mr-3"></i>
                        Settings
                    </a>
                </nav>
            </div>
            <div class="p-4 border-t border-indigo-700">
                <div class="flex items-center">
                    <img class="w-10 h-10 rounded-full" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF' }}" alt="User avatar">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-xs text-indigo-200 hover:text-white">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.querySelector('[aria-controls="mobile-sidebar"]').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.toggle('hidden');
        });
    </script>
</body>
</html>