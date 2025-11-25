<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dataset;
use App\Models\DatasetItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DatasetController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','admin']);
    }

    public function index()
    {
        // ambil datasets dengan jumlah item
        $datasets = Dataset::orderBy('updated_at','desc')->withCount('items')->get();

        // optional: list file storage (stored CSV raw)
        $files = Storage::files('python/datasets'); // atau 'public/python/datasets' kalau kamu simpan di public
        return view('admin.dataset.index', compact('datasets','files'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $dataset = Dataset::create([
            'name' => $request->name,
            'description' => $request->description,
            'rows' => 0
        ]);

        return redirect()->route('admin.dataset.index')->with('success','Dataset created.');
    }

    // create dataset record (no import)
    public function store(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255']);
        $ds = Dataset::create(['name'=>$request->name,'description'=>$request->description ?? '','rows'=>0]);
        return redirect()->route('admin.dataset.index')->with('success','Dataset created.');
    }

    // Import CSV to a dataset (append)
    public function importToDataset(Request $request, Dataset $dataset)
    {
        $request->validate([
            'csvfile' => 'required|file|mimes:csv,txt|max:51200',
            'source' => 'nullable|string'
        ]);

        $file = $request->file('csvfile');
        $path = $file->getRealPath();

        $imported = $this->importFileToDataset($path, $dataset, $request->input('source'));

        // update rows count using actual count from dataset_items
        $dataset->rows = DatasetItem::where('dataset_id', $dataset->id)->count();
        $dataset->save();

        return redirect()->route('admin.dataset.show', $dataset->id)->with('success',"Imported {$imported} rows.");
    }

    // helper: chunk insert
    private function importFileToDataset(string $realPath, Dataset $dataset, ?string $source = null): int
    {
        $handle = fopen($realPath,'r');
        if (!$handle) return 0;

        // read header
        $firstLine = fgets($handle);
        if ($firstLine === false) { fclose($handle); return 0; }
        $firstLine = preg_replace('/^\xEF\xBB\xBF/','',$firstLine);
        $delimiter = $this->detectDelimiter($firstLine);
        $headers = array_map(fn($h)=>Str::lower(trim($h)), str_getcsv($firstLine, $delimiter));

        // reset and skip header
        rewind($handle);
        fgetcsv($handle, 0, $delimiter);

        $batch = [];
        $batchSize = 500;
        $imported = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) === 1 && trim($row[0]) === '') continue;
            if (count($row) !== count($headers)) {
                Log::warning('CSV row mismatch, skipping');
                continue;
            }
            $assoc = array_combine($headers,$row);
            $text = $assoc['text'] ?? $assoc['komentar'] ?? null;
            $label = isset($assoc['label']) && $assoc['label'] !== '' ? (int)$assoc['label'] : null;
            if (!$text || trim($text)==='') continue;

            $batch[] = [
                'dataset_id' => $dataset->id,
                'text' => $text,
                'label' => $label,
                'source' => $source,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($batch) >= $batchSize) {
                DB::table('dataset_items')->insert($batch);
                $imported += count($batch);
                $batch = [];
            }
        }

        if (count($batch)>0) {
            DB::table('dataset_items')->insert($batch);
            $imported += count($batch);
        }

        fclose($handle);
        return $imported;
    }

    private function detectDelimiter(string $line): string
    {
        $counts = [',' => substr_count($line,','), ';' => substr_count($line,';'), "\t" => substr_count($line,"\t")];
        arsort($counts);
        return key($counts);
    }

    public function show(Dataset $dataset)
    {
        // preview first 50 rows, pagination separately
        $items = $dataset->items()->orderBy('id')->paginate(50);
        return view('admin.dataset.show', compact('dataset','items'));
    }

    // API: export dataset as JSON (for FastAPI)
// di App\Http\Controllers\Admin\DatasetController
    public function exportCsv(Dataset $dataset)
    {
        $filename = 'dataset_export_'.$dataset->id.'_'.now()->format('Ymd_His').'.csv';
        $callback = function() use ($dataset) {
            $handle = fopen('php://output','w');
            fputcsv($handle, ['text','label']);
            DatasetItem::where('dataset_id', $dataset->id)
                ->orderBy('id')
                ->chunk(500, function($rows) use ($handle) {
                    foreach ($rows as $r) {
                        fputcsv($handle, [
                            str_replace(["\r","\n"], [' ',' '], $r->text),
                            is_null($r->label) ? '' : $r->label,
                        ]);
                    }
                });
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function apiExportJson(Dataset $dataset)
    {
        // jangan panggil $this->authorize() di sini, karena kita pakai token internal
        $perPage = request()->get('per_page', 1000);
        $page = max(1, (int) request()->get('page', 1));
        $query = DatasetItem::where('dataset_id', $dataset->id)->orderBy('id');
        $total = $query->count();
        $rows = $query->forPage($page, $perPage)->get(['id','text','label']);

        return response()->json([
            'dataset_id' => $dataset->id,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'items' => $rows
        ]);
    }


    // API CSV download (optional)
    public function apiExportCsv(Dataset $dataset)
    {
        $filename = "dataset_{$dataset->id}_" . now()->format('Ymd_His') . ".csv";
        $callback = function() use ($dataset) {
            $handle = fopen('php://output','w');
            fputcsv($handle, ['id','text','label']);
            DatasetItem::where('dataset_id',$dataset->id)->chunk(500, function($rows) use ($handle){
                foreach ($rows as $r) {
                    fputcsv($handle, [$r->id, str_replace(["\r","\n"],[' ',' '],$r->text), is_null($r->label) ? '' : $r->label]);
                }
            });
            fclose($handle);
        };
        return response()->streamDownload($callback, $filename, ['Content-Type'=>'text/csv']);
    }

    public function preview(Dataset $dataset)
    {
        $items = DatasetItem::where('dataset_id', $dataset->id)
            ->orderBy('id')
            ->limit(20)
            ->get();

        return view('admin.dataset.preview', compact('dataset','items'));
    }

    public function destroy(Dataset $dataset)
    {
        DB::transaction(function() use ($dataset) {
            DatasetItem::where('dataset_id', $dataset->id)->delete();
            $dataset->delete();
        });

        return redirect()->route('admin.dataset.index')->with('success','Dataset deleted.');
    }

    // NEW


    // create dataset (store)


    // destroy dataset (hapus record dan dataset_items terkait)


    // import file into dataset (form from modal)


    // API JSON export untuk FastAPI (HILANGkan authorize(), cukup rely pada middleware CheckInternalToken)
 

    // export CSV (existing)


}
