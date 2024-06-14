<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batas_kabupaten', function (Blueprint $table) {
            $table->increments('id');
            $table->multiPolygonZ('geom')->nullable();
            $table->string('kab_kota', 27)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batas_kabupaten');
    }
};
