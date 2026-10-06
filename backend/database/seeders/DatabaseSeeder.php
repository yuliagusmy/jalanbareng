<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure all migrations are executed before seeding
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            ActivationSeeder::class,
            DestinationSeeder::class,
            EventSeeder::class,
            UpdateActivationRelationsSeeder::class,
            UpdateEventDatesSeeder::class,
            PageSeeder::class,
            SettingSeeder::class,
            StorySeeder::class,
            PointSeeder::class,
        ]);
    }
}
