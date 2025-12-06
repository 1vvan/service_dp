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
        Schema::create('client_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('car_model_id')->constrained('car_models')->onDelete('cascade');
            $table->foreignId('fuel_type_id')->constrained('fuel_types')->onDelete('cascade');
            $table->foreignId('engine_type_id')->constrained('engine_types')->onDelete('cascade');
            $table->foreignId('gearbox_type_id')->constrained('gearbox_types')->onDelete('cascade');
            $table->foreignId('drive_unit_type_id')->constrained('drive_unit_types')->onDelete('cascade');
            $table->integer('car_year');
            $table->integer('mileage')->nullable();
            $table->string('vin')->nullable()->unique();
            $table->string('license_plate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_cars');
    }
};
