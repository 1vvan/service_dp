<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use Illuminate\Database\Seeder;

class CarBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Toyota',
            'Volkswagen',
            'Ford',
            'Mercedes-Benz',
            'BMW',
            'Audi',
            'Nissan',
            'Hyundai',
            'Kia',
            'Mazda',
            'Honda',
            'Chevrolet',
            'Renault',
            'Peugeot',
            'Skoda',
            'Volvo',
            'Lexus',
            'Subaru',
            'Jeep',
            'Land Rover',
            'Porsche',
            'Tesla',
            'Mitsubishi',
            'Suzuki',
            'Fiat',
            'Opel',
            'Citroen',
            'Seat',
            'Dacia',
            'Mini',
            'Jaguar',
            'Infiniti',
            'Acura',
            'Genesis',
            'Alfa Romeo',
            'Chrysler',
            'Dodge',
            'Ram',
            'GMC',
            'Cadillac',
            'Lincoln',
        ];

        foreach ($brands as $brand) {
            CarBrand::create(['name' => $brand]);
        }
    }
}
