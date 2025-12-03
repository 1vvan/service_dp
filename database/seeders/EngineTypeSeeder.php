<?php

namespace Database\Seeders;

use App\Models\EngineType;
use Illuminate\Database\Seeder;

class EngineTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $engineTypes = [
            'I3',
            'I4',
            'I6',
            'V6',
            'V8',
            'V10',
            'V12',
            'B4',
            'B6',
        ];

        foreach ($engineTypes as $engineType) {
            EngineType::create(['name' => $engineType]);
        }
    }
}
