<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create additional users for testing
        User::factory(3)->create();

        // Call other seeders in the correct order
        $this->call([
            ClientSeeder::class,
            ProjectSeeder::class,
            TimeEntrySeeder::class,
        ]);
    }
}
