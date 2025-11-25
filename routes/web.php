<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DatasetController;
use App\Http\Controllers\Admin\ScrapingController;
use App\Http\Controllers\Admin\ModelLstmController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Google OAuth Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Video Routes
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/{id}', [VideoController::class, 'show'])->name('videos.show');
    
    // NEW: Route untuk deteksi komentar
    Route::get('/videos/{videoId}/detect-comments', [VideoController::class, 'detectComments'])->name('videos.detectComments'); 
    Route::post('/videos/{videoId}/delete-comments', [VideoController::class, 'deleteDetectedComments'])->name('videos.delete-comments');    
    
    // YouTube Connection (redirect to Google OAuth)
    Route::get('/connect/youtube', [VideoController::class, 'connectYoutube'])->name('connect.youtube');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth','admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/dataset', [DatasetController::class, 'index'])
        ->name('dataset.index');
    // create dataset (POST)
    Route::post('/dataset/create', [DatasetController::class, 'store'])
        ->name('dataset.create');

    // show dataset detail
    Route::get('/dataset/{dataset}', [DatasetController::class, 'show'])
        ->name('dataset.show');

    // IMPORT CSV to dataset
    Route::post('/dataset/import/{dataset}', [DatasetController::class, 'importToDataset'])
        ->name('dataset.import');

    // EXPORT dataset (CSV)
    Route::get('/dataset/export/{dataset}', [DatasetController::class, 'apiExportCsv'])
        ->name('dataset.export');

    // preview small sample
    Route::get('/dataset/preview/{dataset}', [DatasetController::class, 'preview'])
        ->name('dataset.preview');

    // delete dataset
    Route::delete('/dataset/{dataset}', [DatasetController::class, 'destroy'])
        ->name('dataset.destroy');
    
    // Model LSTM

    Route::post('/model/upload', [ModelLstmController::class, 'uploadModel'])->name('model.upload');
    Route::get('/model/download', [ModelLstmController::class, 'download'])->name('model.download');

    Route::get('model', [\App\Http\Controllers\Admin\ModelLstmController::class, 'index'])->name('model.index');
    
    Route::post('/admin/model/trigger-train', [ModelLstmController::class, 'triggerTrain'])
    ->name('model.triggerTrain')
    ->middleware(['auth','admin']);
});
