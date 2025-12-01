<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dataset;
use App\Models\DatasetItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Imports\DatasetItemsImport;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DatasetController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','admin']);
    }

    public function index()
    {
        $datasets = Dataset::orderBy('updated_at','desc')
            ->withCount('items')
            ->get();

        $files = Storage::files('python/datasets');

        return view('admin.dataset.index', compact('datasets','files'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Dataset::create([
            'name'        => $request->name,
            'description' => $request->description,
            'rows'        => 0,
        ]);

        return redirect()
            ->route('admin.dataset.index')
            ->with('success','Dataset created.');
    }

    public function store(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255']);

        Dataset::create([
            'name'        => $request->name,
            'description' => $request->description ?? '',
            'rows'        => 0,
        ]);

        return redirect()
            ->route('admin.dataset.index')
            ->with('success','Dataset created.');
    }

    /**
     * IMPORT PAKAI MAATWEBSITE/EXCEL
     * Route: POST /admin/dataset/import/{dataset}
     * Form field file: datafile
     */
    public function import(Request $request, Dataset $dataset)
    {
        $request->validate([
            'datafile' => 'required|file|mimes:xlsx,xls,csv,txt|max:51200',
        ]);

        try {
            $path = $request->file('datafile')->getPathname();

            $spreadsheet = IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true); // A,B,C,...

            // Asumsi:
            // Kolom A = text/komentar
            // Kolom B = label
            foreach ($rows as $index => $row) {
                // kalau baris pertama header, bisa skip:
                if ($index === 1) {
                    continue;
                }

                $text = $row['A'] ?? null;
                $labelRaw = $row['B'] ?? null;

                if (!$text || trim((string)$text) === '') {
                    continue;
                }

                $label = null;
                if ($labelRaw !== null && $labelRaw !== '') {
                    $val = strtolower(trim((string) $labelRaw));
                    if ($val === '1' || str_contains($val, 'judi') || str_contains($val, 'slot')) {
                        $label = 1;
                    } elseif ($val === '0' || $val === 'normal') {
                        $label = 0;
                    }
                }

                DatasetItem::create([
                    'dataset_id' => $dataset->id,
                    'comment_id' => null,
                    'text'       => $text,
                    'label'      => $label,
                ]);
            }

            $dataset->rows = DatasetItem::where('dataset_id', $dataset->id)->count();
            $dataset->save();

            return redirect()
                ->route('admin.dataset.show', $dataset->id)
                ->with('success', 'Import dataset berhasil.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal import: '.$e->getMessage());
        }
    }

    public function show(Dataset $dataset)
    {
        $items = DatasetItem::where('dataset_id', $dataset->id)
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.dataset.show', [
            'dataset' => $dataset,
            'items'   => $items,
        ]);
    }

    // exportCsv, apiExportJson, apiExportCsv, preview, destroy tetap seperti punyamu
    // (tidak kuubah di sini supaya jawaban nggak kepanjangan)
}
