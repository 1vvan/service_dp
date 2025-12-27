<?php

namespace Database\Seeders;

use App\Models\PaymentStatus;
use Illuminate\Database\Seeder;

class PaymentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['id' => 1, 'name' => 'Очікується'],
            ['id' => 2, 'name' => 'Завершено'],
            ['id' => 3, 'name' => 'Помилка'],
        ];

        foreach ($statuses as $status) {
            PaymentStatus::updateOrCreate(
                ['id' => $status['id']],
                ['name' => $status['name']]
            );
        }
    }
}

