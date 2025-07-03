<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Seeder;

class TimeEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users and projects
        $users = User::all();
        
        // Create 10 time entries for each project
        Project::all()->each(function ($project) use ($users) {
            // Randomly select a user for each time entry
            TimeEntry::factory(10)->create([
                'project_id' => $project->id,
                'user_id' => $users->random()->id,
            ]);
        });
    }
}