<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_type_id');
            $table->string('plate_number');
            $table->integer('accumulated_time')->default(0);
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};