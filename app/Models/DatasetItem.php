<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatasetItem extends Model
{
    protected $fillable = ['dataset_id','text','label','source'];
    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }
}
