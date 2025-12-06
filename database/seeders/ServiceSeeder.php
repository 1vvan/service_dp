<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Заміна масла', 'base_price' => 500.00],
            ['name' => 'Заміна фільтрів', 'base_price' => 300.00],
            ['name' => 'Діагностика двигуна', 'base_price' => 700.00],
            ['name' => 'Ремонт гальмівної системи', 'base_price' => 1200.00],
            ['name' => 'Заміна свічок запалювання', 'base_price' => 6000.00],
            ['name' => 'Перевірка та ремонт підвіски', 'base_price' => 2200.00],
            ['name' => 'Заміна ременя ГРМ', 'base_price' => 8000.00],
            ['name' => 'Промивка системи охолодження', 'base_price' => 2000.00],
            ['name' => 'Регулювання розвал-збіжності', 'base_price' => 500.00],
            ['name' => 'Комп\'ютерна діагностика', 'base_price' => 800.00],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['name' => $service['name']], $service);
        }
    }
}
