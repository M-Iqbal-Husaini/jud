<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_info', function (Blueprint $table) {
            $table->id();

            // Relasi ke dataset (opsional, sesuaikan nama tabel & kolommu)
            $table->unsignedBigInteger('dataset_id')->nullable();

            // Info dasar model
            $table->string('model_name');              // misal: "RandomForest v1"
            $table->string('framework')->nullable();   // misal: "sklearn", "pytorch", dll
            $table->string('status')->default('trained'); // trained / failed / training

            // Metric training
            $table->float('train_accuracy')->nullable();
            $table->float('val_accuracy')->nullable();
            $table->float('train_loss')->nullable();
            $table->float('val_loss')->nullable();

            // Info training lain
            $table->integer('epochs')->nullable();
            $table->json('hyperparameters')->nullable();  // simpan setting training dalam JSON

            // Lokasi file model yang di-save
            $table->string('model_path')->nullable();     // path file .pkl / .pt / dll

            // Waktu training selesai
            $table->timestamp('trained_at')->nullable();

            $table->timestamps();

            // Kalau mau foreign key ke tabel datasets
            // $table->foreign('dataset_id')->references('id')->on('datasets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_info');
    }
};
