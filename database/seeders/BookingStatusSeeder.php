<?php

namespace Database\Seeders;

use App\Models\BookingStatus;
use Illuminate\Database\Seeder;

class BookingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Нова',
            'Підтверджена',
            'В роботі',
            'Виконана',
            'Скасована',
        ];

        foreach ($statuses as $status) {
            BookingStatus::create(['name' => $status]);
        }
    }
}
