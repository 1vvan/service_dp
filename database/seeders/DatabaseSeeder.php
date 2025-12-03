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

        // Создаем тестового пользователя-менеджера
        User::create([
            'full_name' => 'Test Manager',
            'email' => 'manager@example.com',
            'phone' => '+380501234567',
            'role_id' => User::ROLE_MANAGER,
            'password' => bcrypt('password'),
        ]);

        // Создаем тестового админа
        User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '+380507654321',
            'role_id' => User::ROLE_ADMIN,
            'password' => bcrypt('password'),
        ]);
    }
}
