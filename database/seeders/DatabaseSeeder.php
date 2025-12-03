<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CarBrandSeeder::class,
            CarModelSeeder::class,
            FuelTypeSeeder::class,
            EngineTypeSeeder::class,
            GearboxTypeSeeder::class,
            DriveUnitTypeSeeder::class,
            BookingStatusSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
