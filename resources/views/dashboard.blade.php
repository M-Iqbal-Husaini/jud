@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Selamat datang, {{ Auth::user()->name }}!</h2>
                <p class="text-gray-600">Apa yang ingin Anda lakukan hari ini?</p>
            </div>
            <div class="bg-indigo-100 p-3 rounded-full">
                <i class="fas fa-gem text-indigo-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Total Pengunjung</p>
                    <h3 class="text-2xl font-bold">1,254</h3>
                    <p class="text-green-500 text-sm">+12% dari bulan lalu</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Total Penjualan</p>
                    <h3 class="text-2xl font-bold">$8,542</h3>
                    <p class="text-green-500 text-sm">+8% dari bulan lalu</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Proyek Aktif</p>
                    <h3 class="text-2xl font-bold">24</h3>
                    <p class="text-red-500 text-sm">-2 dari bulan lalu</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-project-diagram text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Pesan Baru</p>
                    <h3 class="text-2xl font-bold">12</h3>
                    <p class="text-green-500 text-sm">+4 dari kemarin</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-envelope text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Projects -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Projects -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Proyek Terkini</h3>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-start p-4 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <div class="bg-indigo-100 p-3 rounded-full mr-4">
                        <i class="fas fa-code text-indigo-600"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">Redesign Website</h4>
                        <p class="text-sm text-gray-600">Due in 3 days</p>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <span class="mr-2">Progress:</span>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: 65%"></div>
                            </div>
                            <span class="ml-2">65%</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-start p-4 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-mobile-alt text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">Mobile App Development</h4>
                        <p class="text-sm text-gray-600">Due in 1 week</p>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <span class="mr-2">Progress:</span>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 40%"></div>
                            </div>
                            <span class="ml-2">40%</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-start p-4 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <div class="bg-yellow-100 p-3 rounded-full mr-4">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">Analytics Dashboard</h4>
                        <p class="text-sm text-gray-600">Due in 2 weeks</p>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <span class="mr-2">Progress:</span>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: 15%"></div>
                            </div>
                            <span class="ml-2">15%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terkini</h3>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                        <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800">Pengguna baru <span class="font-semibold">Sarah Johnson</span> terdaftar</p>
                        <p class="text-xs text-gray-500">2 jam yang lalu</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                        <i class="fas fa-shopping-cart text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800">Pesanan baru #ORD-2023-456 diterima</p>
                        <p class="text-xs text-gray-500">5 jam yang lalu</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="bg-purple-100 p-2 rounded-full mr-3">
                        <i class="fas fa-ticket-alt text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800">Tiket dukungan #TKT-789 dibuka</p>
                        <p class="text-xs text-gray-500">1 hari yang lalu</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="bg-indigo-100 p-2 rounded-full mr-3">
                        <i class="fas fa-code text-indigo-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800">Versi baru 2.3.0 dirilis</p>
                        <p class="text-xs text-gray-500">2 hari yang lalu</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="bg-yellow-100 p-2 rounded-full mr-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800">Peringatan server pada 10:30 AM</p>
                        <p class="text-xs text-gray-500">3 hari yang lalu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection