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
        Schema::table('client_cars', function (Blueprint $table) {
            $table->foreignId('fuel_type_id')->nullable()->change();
            $table->foreignId('engine_type_id')->nullable()->change();
            $table->foreignId('gearbox_type_id')->nullable()->change();
            $table->foreignId('drive_unit_type_id')->nullable()->change();
            $table->integer('mileage')->nullable(false)->change();
            $table->string('vin')->nullable(false)->change();
            $table->string('license_plate')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_cars', function (Blueprint $table) {
            $table->foreignId('fuel_type_id')->nullable(false)->change();
            $table->foreignId('engine_type_id')->nullable(false)->change();
            $table->foreignId('gearbox_type_id')->nullable(false)->change();
            $table->foreignId('drive_unit_type_id')->nullable(false)->change();
            $table->integer('mileage')->nullable()->change();
            $table->string('vin')->nullable()->change();
            $table->string('license_plate')->nullable()->change();
        });
    }
};
