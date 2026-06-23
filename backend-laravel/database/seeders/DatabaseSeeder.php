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
        // Create default admin user
        User::factory()->create([
            'name' => 'Admin NusaFlow',
            'email' => 'admin@nusaflow.test',
            'role' => 'super_admin',
        ]);

        // Create a test tourist user
        User::factory()->create([
            'name' => 'Wisatawan Test',
            'email' => 'tourist@nusaflow.test',
            'role' => 'tourist',
        ]);

        // Run domain seeders in order
        $this->call([
            DestinationCategorySeeder::class,
            DestinationSeeder::class,
            VisitorLogSeeder::class,
            EventSeeder::class,
            CrowdPredictionSeeder::class,
        ]);
    }
}
