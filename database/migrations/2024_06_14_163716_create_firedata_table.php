<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('firedata', function (Blueprint $table) {
            $table->id();
            $table->float('brightness');
            $table->float('brightness_2');
            $table->geometry('geom', 4326);
            $table->timestamps();
            $table->float('scan');
            $table->float('track');
            $table->timestamp('acq_datetime');
            $table->char('confidence');
            $table->float('frp');
        });
    }

    public function down()
    {
        Schema::dropIfExists('firedata');
    }
};
