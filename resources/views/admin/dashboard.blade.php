@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm px-6 py-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Selamat datang, Administrator!
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Ringkasan statistik sistem & model pendeteksi komentar judi online.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">
            <i class="fas fa-sync-alt mr-2"></i> Refresh
        </a>
    </div>

    {{-- Statistik utama --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Pengguna --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Pengguna</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                </div>
                <div class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-600 rounded-full">
                    <i class="fas fa-users text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Total Admin --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Admin</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalAdmins }}</p>
                </div>
                <div class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-600 rounded-full">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Total Dataset --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Dataset</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalDataset ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-600 rounded-full">
                    <i class="fas fa-database text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Model info + sistem --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Informasi Model LSTM --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Informasi Model LSTM</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Ringkasan performa model teks terkini.
                    </p>
                </div>

                {{-- Status badge --}}
                @php
                    $status = optional($modelInfo)->status ?? 'belum';
                @endphp

                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    @if($status === 'trained')
                        bg-green-100 text-green-700
                    @elseif($status === 'training')
                        bg-yellow-100 text-yellow-700
                    @elseif($status === 'failed')
                        bg-red-100 text-red-700
                    @else
                        bg-gray-100 text-gray-600
                    @endif">
                    @if($status === 'trained')
                        ● Trained
                    @elseif($status === 'training')
                        ● Sedang training
                    @elseif($status === 'failed')
                        ● Gagal training
                    @else
                        ● Belum ada model
                    @endif
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Akurasi --}}
                <div class="rounded-xl bg-red-50 px-4 py-3 flex flex-col justify-between">
                    <div class="text-[11px] font-medium tracking-wide text-red-500 mb-1">
                        AKURASI (VAL)
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-red-600 leading-tight">
                        @if($modelAccuracy !== null)
                            {{ number_format($modelAccuracy * 100, 2) }}%
                        @else
                            0%
                        @endif
                    </div>
                    <div class="text-[11px] text-red-400 mt-1">
                        {{ $modelInfo ? $modelInfo->model_name : 'Belum ada model' }}
                    </div>
                </div>

                {{-- Loss --}}
                <div class="rounded-xl bg-red-50 px-4 py-3 flex flex-col justify-between">
                    <div class="text-[11px] font-medium tracking-wide text-red-500 mb-1">
                        LOSS (VAL)
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-red-600 leading-tight">
                        @if($modelLoss !== null)
                            {{ number_format($modelLoss, 3) }}
                        @else
                            0.000
                        @endif
                    </div>
                    <div class="text-[11px] text-red-400 mt-1">
                        Semakin kecil semakin baik.
                    </div>
                </div>

                {{-- Epoch --}}
                <div class="rounded-xl bg-red-50 px-4 py-3 flex flex-col justify-between">
                    <div class="text-[11px] font-medium tracking-wide text-red-500 mb-1">
                        EPOCH
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-red-600 leading-tight">
                        {{ $modelEpochs ?? '-' }}
                    </div>
                    <div class="text-[11px] text-red-400 mt-1">
                        Jumlah iterasi training.
                    </div>
                </div>

                {{-- Terakhir training --}}
                <div class="rounded-xl bg-red-50 px-4 py-3 flex flex-col justify-between">
                    <div class="text-[11px] font-medium tracking-wide text-red-500 mb-1">
                        TERAKHIR TRAINING
                    </div>
                    <div class="text-sm font-semibold text-red-600 leading-snug">
                        @if($modelTrainedAt)
                            {{ $modelTrainedAt->setTimezone(config('app.timezone'))->format('d M Y') }}<br>
                            <span class="text-xs font-normal text-red-500">
                                {{ $modelTrainedAt->setTimezone(config('app.timezone'))->format('H:i') }} WIB
                            </span>
                        @else
                            Belum Pernah
                        @endif
                    </div>
                    <div class="text-[11px] text-red-400 mt-1">
                        Update otomatis setiap selesai training.
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-between">
                <div class="text-xs text-gray-500">
                    Sumber data: tabel <span class="font-semibold">model_info</span>.
                </div>

                <a href="{{ route('admin.model.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-500">
                    Kelola Model
                </a>
            </div>
        </div>

        {{-- Info Sistem --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">
                Info Sistem
            </h2>

            <ul class="space-y-3 text-sm text-gray-700">
                <li class="flex items-start">
                    <span class="text-red-500 mt-1">
                        <i class="fas fa-circle text-xs"></i>
                    </span>
                    <p class="ml-2">
                        FastAPI harus aktif agar deteksi komentar bekerja.
                    </p>
                </li>

                <li class="flex items-start">
                    <span class="text-red-500 mt-1">
                        <i class="fas fa-circle text-xs"></i>
                    </span>
                    <p class="ml-2">
                        Import dataset melalui menu <b>Dataset</b> admin.
                    </p>
                </li>

                <li class="flex items-start">
                    <span class="text-red-500 mt-1">
                        <i class="fas fa-circle text-xs"></i>
                    </span>
                    <p class="ml-2">
                        Tekan tombol <b>Train Model</b> untuk update akurasi.
                    </p>
                </li>
            </ul>
        </div>

    </div>

</div>
@endsection
