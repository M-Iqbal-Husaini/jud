<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModelInternalController extends Controller
{
    public function uploadTrainedModel(Request $request)
    {
        $request->validate([
            'model_file' => 'required|file'
        ]);

        $file = $request->file('model_file');
        $filename = 'model_' . time() . '.h5';

        $file->storeAs('python/models', $filename, 'public');

        return response()->json([
            'message' => 'Model received & saved',
            'filename' => $filename
        ]);
    }
}
