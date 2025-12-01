<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModelInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class ModelLstmController extends Controller
{
    protected string $modelsDir = 'python/models';

    public function __construct()
    {
        $this->middleware(['auth', 'admin'])
            ->except(['internalTrainCallback']); // <--- BEBASKAN CALLBACK
    }

    public function index()
    {
        $dir = public_path($this->modelsDir);

        $latestModel = null;
        $latestPy = null;
        $modelModified = null;
        $pyModified = null;

        if (is_dir($dir)) {
            $files = File::files($dir);

            if (!empty($files)) {
                $h5 = array_filter($files, fn($f) => strtolower($f->getExtension()) === 'h5');
                if (!empty($h5)) {
                    usort($h5, fn($a, $b) => filemtime($a) <=> filemtime($b));
                    $last = end($h5);
                    $latestModel = str_replace(public_path(), '', $last->getPathname());
                    $modelModified = filemtime($last->getPathname());
                }

                $py = array_filter($files, fn($f) => strtolower($f->getExtension()) === 'py');
                if (!empty($py)) {
                    usort($py, fn($a, $b) => filemtime($a) <=> filemtime($b));
                    $last = end($py);
                    $latestPy = str_replace(public_path(), '', $last->getPathname());
                    $pyModified = filemtime($last->getPathname());
                }
            }
        }

        return view('admin.model.index', [
            'modelFilePath'  => $latestModel,
            'trainFilePath'  => $latestPy,
            'modelModified'  => $modelModified,
            'trainModified'  => $pyModified,
        ]);
    }

    public function uploadModel(Request $request)
    {
        $request->validate([
            'model_file' => 'required|file|max:51200',
        ]);

        $file = $request->file('model_file');
        $original = $file->getClientOriginalName();
        $filename = time() . '_' . preg_replace('/\s+/', '_', $original);

        $dest = public_path($this->modelsDir);
        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }

        try {
            $file->move($dest, $filename);
        } catch (\Throwable $e) {
            Log::error('Model upload failed: ' . $e->getMessage());
            return redirect()->route('admin.model.index')
                ->with('error', 'Upload gagal: ' . $e->getMessage());
        }

        return redirect()->route('admin.model.index')
            ->with('success', 'File uploaded: ' . $filename);
    }

    public function download()
    {
        $dir = public_path($this->modelsDir);

        if (!is_dir($dir)) {
            return back()->with('error', 'Folder model tidak ditemukan.');
        }

        $files = File::files($dir);
        $h5 = array_filter($files, fn($f) => strtolower($f->getExtension()) === 'h5');

        if (empty($h5)) {
            return back()->with('error', 'Tidak ada file .h5 model untuk di-download.');
        }

        usort($h5, fn($a, $b) => filemtime($a) <=> filemtime($b));
        $full = end($h5)->getPathname();

        return Response::download($full);
    }

    /**
     * Jalankan script .py langsung dengan Process.
     * Setelah sukses, simpan info ke tabel model_info.
     */
    public function train()
    {
        $dir = public_path($this->modelsDir);

        if (!is_dir($dir)) {
            return back()->with('error', 'Folder model belum ada: ' . $dir);
        }

        $files = File::files($dir);
        $py = array_filter($files, fn($f) => strtolower($f->getExtension()) === 'py');

        if (empty($py)) {
            return back()->with('error', 'Tidak ada file .py di folder models.');
        }

        usort($py, fn($a, $b) => filemtime($a) <=> filemtime($b));
        $latest = end($py);
        $script = $latest->getPathname();

        try {
            $python = 'python';
            $process = new Process([$python, $script]);
            $process->setTimeout(3600);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Training error: ' . $process->getErrorOutput());
                return back()->with('error', 'Training error: ' . substr($process->getErrorOutput(), 0, 500));
            }

            // Catat info model ke database
            ModelInfo::create([
                'dataset_id'      => null,
                'model_name'      => 'LSTM Local Training',
                'framework'       => 'keras-tensorflow',
                'status'          => 'trained',
                'train_accuracy'  => null,
                'val_accuracy'    => null,
                'train_loss'      => null,
                'val_loss'        => null,
                'epochs'          => null,
                'hyperparameters' => null,
                'model_path'      => $this->modelsDir,
                'trained_at'      => Carbon::now(),
            ]);

            return back()->with('success', 'Training selesai. Output: ' . substr($process->getOutput(), 0, 500));
        } catch (\Throwable $e) {
            Log::error('Train exception: ' . $e->getMessage());
            return back()->with('error', 'Exception saat training: ' . $e->getMessage());
        }
    }

    /**
     * Trigger training via FastAPI (background).
     * Di sini kita create record model_info dulu, kirim ID-nya ke FastAPI.
     */
    public function triggerTrain(Request $request)
    {
        $request->validate([
            'dataset_id'        => 'required|integer',
            'epochs'            => 'nullable|integer',
            'max_words'         => 'nullable|integer',
            'maxlen'            => 'nullable|integer',
            'model_name_prefix' => 'nullable|string',
        ]);

        $datasetId   = (int) $request->input('dataset_id');
        $epochs      = (int) $request->input('epochs', 3);
        $maxWords    = (int) $request->input('max_words', 20000);
        $maxlen      = (int) $request->input('maxlen', 200);
        $namePrefix  = $request->input('model_name_prefix', 'admin_run');

        // 1) Buat nama model unik
        $modelName = $namePrefix . '_' . now()->format('Ymd_His');

        // 2) Buat record awal di model_info (status: queued/training)
        $modelInfo = ModelInfo::create([
            'dataset_id'      => $datasetId,
            'model_name'      => $modelName,
            'framework'       => 'keras-tensorflow',
            'status'          => 'queued',
            'epochs'          => $epochs,
            'hyperparameters' => [
                'max_words' => $maxWords,
                'maxlen'    => $maxlen,
            ],
            'trained_at'      => null,
        ]);

        // 3) Payload ke FastAPI
        $payload = [
            'dataset_id'        => $datasetId,
            'epochs'            => $epochs,
            'max_words'         => $maxWords,
            'maxlen'            => $maxlen,
            'model_name_prefix' => $namePrefix,
            'model_info_id'     => $modelInfo->id, // penting untuk callback update
        ];

        $fastapiUrl = rtrim(env('FASTAPI_URL', 'http://127.0.0.1:8001'), '/')
            . '/internal/train/background';

        try {
            $resp = Http::withHeaders([
                'X-INTERNAL-TOKEN' => env('INTERNAL_API_TOKEN', 'HAIKYU2025'),
            ])
                ->timeout(3)
                ->connectTimeout(2)
                ->post($fastapiUrl, $payload);

            Log::info("FastAPI train request status=" . $resp->status(), [
                'body'   => $resp->body(),
                'url'    => $fastapiUrl,
                'payload'=> $payload,
            ]);

            // Update status jadi "training" kalau request FastAPI tidak error
            $modelInfo->update([
                'status' => 'training',
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // dianggap sukses — fire and forget
            Log::warning("FastAPI background fire-and-forget: " . $e->getMessage());
            $modelInfo->update([
                'status' => 'training',
            ]);
        } catch (\Exception $e) {
            Log::error("FastAPI train error: " . $e->getMessage());
            $modelInfo->update([
                'status' => 'failed',
            ]);
            return back()->with('error', 'Failed contacting FastAPI: ' . $e->getMessage());
        }

        return back()->with('success', 'Training started (FastAPI background).');
    }

    /**
     * Endpoint callback yang dipanggil FastAPI ketika training selesai.
     * URL: POST /api/internal/train/callback
     */
    public function internalTrainCallback(Request $request)
    {
        // Simple security: cek token internal
        $token = $request->header('X-INTERNAL-TOKEN');
        if ($token !== env('INTERNAL_API_TOKEN', 'HAIKYU2025')) {
            Log::warning('Invalid INTERNAL_API_TOKEN on callback');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'model_info_id'  => 'required|integer',
            'status'         => 'required|string', // trained / failed
            'train_accuracy' => 'nullable|numeric',
            'val_accuracy'   => 'nullable|numeric',
            'train_loss'     => 'nullable|numeric',
            'val_loss'       => 'nullable|numeric',
            'epochs'         => 'nullable|integer',
            'model_path'     => 'nullable|string',
        ]);

        $modelInfo = ModelInfo::find($data['model_info_id']);
        if (!$modelInfo) {
            Log::error('ModelInfo not found on callback id=' . $data['model_info_id']);
            return response()->json(['message' => 'ModelInfo not found'], 404);
        }

        $modelInfo->status         = $data['status'];
        $modelInfo->train_accuracy = $data['train_accuracy'] ?? null;
        $modelInfo->val_accuracy   = $data['val_accuracy'] ?? null;
        $modelInfo->train_loss     = $data['train_loss'] ?? null;
        $modelInfo->val_loss       = $data['val_loss'] ?? null;
        $modelInfo->epochs         = $data['epochs'] ?? $modelInfo->epochs;
        $modelInfo->model_path     = $data['model_path'] ?? $modelInfo->model_path;

        if ($data['status'] === 'trained') {
            $modelInfo->trained_at = Carbon::now();
        }

        $modelInfo->save();

        return response()->json([
            'message'    => 'Model info updated',
            'model_info' => $modelInfo,
        ]);
    }
}
