<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Runs with: php artisan db:seed
     * Or all together with: php artisan migrate --seed
     *
     * Every seeder below is safe to run again. It updates the same rows
     * instead of creating duplicates.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SettingSeeder::class,
            RoomSeeder::class,
            ServiceSeeder::class,
            BookingSeeder::class,
            ContactSeeder::class,
        ]);
    }
}
