<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('rows')->default(0);
            $table->timestamps();
        });

        Schema::create('dataset_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained('datasets')->cascadeOnDelete();
            $table->text('text');
            $table->integer('label')->nullable(); // gunakan integer (0/1) atau null
            $table->string('source')->nullable(); // optional: file name or uploaded_by
            $table->timestamps();

            $table->index(['dataset_id','label']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('datasets');
        Schema::dropIfExists('dataset_items');
    }
};