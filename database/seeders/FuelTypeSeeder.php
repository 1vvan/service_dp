<?php

namespace Database\Seeders;

use App\Models\FuelType;
use Illuminate\Database\Seeder;

class FuelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fuelTypes = [
            'Бензин',
            'Дизель',
            'Гібрид',
            'Електричний',
            'Газ (LPG)',
            'Газ (CNG)',
        ];

        foreach ($fuelTypes as $fuelType) {
            FuelType::create(['name' => $fuelType]);
        }
    }
}
