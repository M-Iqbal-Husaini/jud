<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    protected $fillable = ['name','description','rows'];
    public function items()
    {
        return $this->hasMany(DatasetItem::class);
    }
}
