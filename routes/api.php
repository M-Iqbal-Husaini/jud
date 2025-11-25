<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DatasetController;
use App\Http\Controllers\Internal\ModelInternalController;
use App\Http\Controllers\Internal\DatasetInternalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('CheckInternalToken')->prefix('internal')->group(function () {

    // FastAPI ambil dataset json
    Route::get('/dataset/{dataset}', [DatasetInternalController::class, 'exportJson'])
        ->name('internal.dataset.json');

    // FastAPI upload model hasil training (jika perlu)
    Route::post('/model/upload', [ModelInternalController::class, 'uploadTrainedModel'])
        ->name('internal.model.upload');

    Route::middleware('CheckInternalToken')->prefix('internal')->group(function () {
    Route::get('/dataset/{dataset}', [DatasetInternalController::class, 'show'])->name('internal.dataset.json');
    });
});