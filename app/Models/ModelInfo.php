<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelInfo extends Model
{
    use HasFactory;

    protected $table = 'model_info';

    protected $fillable = [
        'dataset_id',
        'model_name',
        'framework',
        'status',
        'train_accuracy',
        'val_accuracy',
        'train_loss',
        'val_loss',
        'epochs',
        'hyperparameters',
        'model_path',
        'trained_at',
    ];

    protected $casts = [
        'hyperparameters' => 'array',
        'trained_at' => 'datetime',
    ];

    // Contoh relasi ke Dataset (kalau ada Model Dataset)
    // public function dataset()
    // {
    //     return $this->belongsTo(Dataset::class);
    // }
}
