<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'ТО' => ServiceCategory::updateOrCreate(['name' => 'ТО']),
            'Діагностика' => ServiceCategory::updateOrCreate(['name' => 'Діагностика']),
            'Ремонт' => ServiceCategory::updateOrCreate(['name' => 'Ремонт']),
        ];

        $services = [
            ['name' => 'Заміна моторної оливи',           'base_price' => 800.00,  'category' => 'ТО'],
            ['name' => 'Обслуговування гальмівної системи','base_price' => 1800.00, 'category' => 'Ремонт'],
            ['name' => 'Ротація шин',                     'base_price' => 600.00,  'category' => 'ТО'],
            ['name' => 'Комплексна діагностика авто',      'base_price' => 1200.00, 'category' => 'Діагностика'],
            ['name' => 'Діагностика двигуна',              'base_price' => 900.00,  'category' => 'Діагностика'],
            ['name' => 'Обслуговування кондиціонера',      'base_price' => 1500.00, 'category' => 'ТО'],
            ['name' => 'Обслуговування трансмісії',        'base_price' => 2500.00, 'category' => 'ТО'],
            ['name' => 'Заміна акумулятора',               'base_price' => 500.00,  'category' => 'Ремонт'],
            ['name' => 'Регулювання розвал-сходження',     'base_price' => 1000.00, 'category' => 'Ремонт'],
            ['name' => 'Промивка системи охолодження',     'base_price' => 1700.00, 'category' => 'ТО'],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                [
                    'base_price' => $service['base_price'],
                    'category_id' => $categories[$service['category']]->id,
                ]
            );
        }
    }
}
