@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-3">
                <h5>Total Users</h5>
                <p class="display-4 mb-0">{{ $totalUsers }}</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3">
                <h5>Total Admins</h5>
                <p class="display-4 mb-0">{{ $totalAdmins }}</p>
            </div>
        </div>

        <!-- contoh card tambahan -->
        <div class="col-md-4">
            <div class="card p-3">
                <h5>Quick Actions</h5>
                <div class="mt-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-primary">Refresh</a>
                    {{-- Tambah link ke manajemen user/video dll --}}
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h4>Recent Activity (placeholder)</h4>
        <p class="text-muted">Anda bisa menampilkan log, aktivitas pengguna, atau hasil deteksi di sini.</p>
    </div>
</div>
@endsection
