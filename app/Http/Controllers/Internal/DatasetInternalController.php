<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dataset;
use App\Models\DatasetItem;

class DatasetInternalController extends Controller
{
    /**
     * Return paginated JSON for given dataset id.
     * Protected only by CheckInternalToken middleware (set in routes/api.php).
     */
    public function exportJson(Request $request, $dataset)
    {
        $perPage = (int) $request->get('per_page', 1000);
        $page = max(1, (int) $request->get('page', 1));

        // ensure dataset exists
        $ds = Dataset::find($dataset);
        if (!$ds) {
            return response()->json(['error' => 'Dataset not found'], 404);
        }

        $query = DatasetItem::where('dataset_id', $ds->id)->orderBy('id');
        $total = $query->count();
        $rows = $query->forPage($page, $perPage)->get(['id','text','label']);

        return response()->json([
            'dataset_id' => $ds->id,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'items' => $rows
        ]);
    }
}
