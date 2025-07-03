<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 projects for each client
        Client::all()->each(function ($client) {
            Project::factory(2)->create([
                'client_id' => $client->id,
            ]);
        });
    }
}