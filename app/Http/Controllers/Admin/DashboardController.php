<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Tampilkan halaman dashboard admin sederhana dengan beberapa statistik.
     */
    public function index(Request $request)
    {
        // Contoh statistik sederhana -- ubah sesuai kebutuhan
        $totalUsers = User::count();
        $totalAdmins = User::where('is_admin', 1)->count();

        // Jika Anda menyimpan data video di DB, tambahkan query disini.
        // $totalVideos = DB::table('videos')->count();

        return view('admin.dashboard', compact('totalUsers', 'totalAdmins'));
    }
}
