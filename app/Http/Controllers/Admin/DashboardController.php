<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Dataset;
use App\Models\ModelInfo;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Tampilkan halaman dashboard admin sederhana dengan beberapa statistik.
     */
    public function index()
    {
        // statistik lain (contoh, sesuaikan dengan project-mu)
        $totalUsers   = User::count();
        $totalAdmins  = User::where('is_admin', true)->count();
        $totalDataset = Dataset::count();

        // === AMBIL MODEL LSTM TERAKHIR YANG SUDAH TRAINED ===
        $latestModel = ModelInfo::where('status', 'trained')
            ->orderByDesc('trained_at')
            ->orderByDesc('id')
            ->first();

        $accuracy = $latestModel ? $latestModel->val_accuracy ?? $latestModel->train_accuracy : null;
        $loss     = $latestModel ? $latestModel->val_loss ?? $latestModel->train_loss : null;
        $epochs   = $latestModel ? $latestModel->epochs : null;
        $trainedAt= $latestModel ? $latestModel->trained_at : null;

        return view('admin.dashboard', [
            'totalUsers'   => $totalUsers,
            'totalAdmins'  => $totalAdmins,
            'totalDataset' => $totalDataset,

            'modelInfo'    => $latestModel,
            'modelAccuracy'=> $accuracy,
            'modelLoss'    => $loss,
            'modelEpochs'  => $epochs,
            'modelTrainedAt' => $trainedAt,
        ]);
    }
}
