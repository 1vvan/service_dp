<?php

namespace Database\Seeders;

use App\Models\GearboxType;
use Illuminate\Database\Seeder;

class GearboxTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gearboxTypes = [
            'Механічна',
            'Автоматична',
            'Роботизована',
            'Варіатор (CVT)',
        ];

        foreach ($gearboxTypes as $gearboxType) {
            GearboxType::create(['name' => $gearboxType]);
        }
    }
}
