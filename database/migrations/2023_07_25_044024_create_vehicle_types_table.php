<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('parking_cost_per_minute', 8, 2)->default(0);
            $table->string('key');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_types');
    }
};