<?php

namespace Database\Seeders;

use App\Models\DriveUnitType;
use Illuminate\Database\Seeder;

class DriveUnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $driveUnitTypes = [
            'Передній привід (FWD)',
            'Задній привід (RWD)',
            'Повний привід (AWD)',
        ];

        foreach ($driveUnitTypes as $driveUnitType) {
            DriveUnitType::create(['name' => $driveUnitType]);
        }
    }
}
