<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Jika ADMIN → redirect ke admin dashboard
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Jika USER BIASA → tampilkan dashboard user
        return view('dashboard', compact('user'));
    }
}
