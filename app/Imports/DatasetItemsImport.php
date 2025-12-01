<?php

namespace App\Imports;

use App\Models\DatasetItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DatasetItemsImport implements ToModel, WithHeadingRow
{
    protected int $datasetId;

    public function __construct(int $datasetId)
    {
        $this->datasetId = $datasetId;
    }

    /**
     * Baris header (kolom text, label) ada di row ke-1
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Tiap baris di file akan masuk ke sini sebagai array asosiatif:
     * [
     *   'text'  => 'komentar...',
     *   'label' => 0/1/normal/judi/...
     * ]
     */
    public function model(array $row)
    {
        // ambil teks dari kolom 'text' atau 'komentar'
        $text = $row['text'] ?? $row['komentar'] ?? null;
        if (!$text || trim((string) $text) === '') {
            return null; // skip baris kosong
        }

        $labelRaw = $row['label'] ?? null;
        $label    = null;

        if ($labelRaw !== null && $labelRaw !== '') {
            $val = strtolower(trim((string) $labelRaw));

            // fleksibel: 1 / 'judi' / ada kata 'judi' / 'slot' dianggap 1
            if ($val === '1' || $val === 'judi' || str_contains($val, 'judi') || str_contains($val, 'slot')) {
                $label = 1;
            } elseif ($val === '0' || $val === 'normal') {
                $label = 0;
            }
        }

        return new DatasetItem([
            'dataset_id' => $this->datasetId,
            'comment_id' => null,   // kalau di file belum ada kolom comment_id
            'text'       => $text,
            'label'      => $label,
            // kalau tabelmu punya kolom 'source', boleh isi di sini
            // 'source'     => null,
        ]);
    }
}
