<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-3 months', 'now');
        $startTime = clone $date;
        $startTime->setTime(
            $this->faker->numberBetween(8, 17),
            $this->faker->randomElement([0, 15, 30, 45])
        );

        $endTime = clone $startTime;
        $endTime->modify('+' . $this->faker->numberBetween(1, 8) . ' hours');

        $durationMinutes = $startTime->diff($endTime)->h * 60 + $startTime->diff($endTime)->i;

        // 50% chance to use start/end time, 50% chance to use duration only
        $useStartEndTime = $this->faker->boolean();

        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'date' => $date,
            'start_time' => $useStartEndTime ? $startTime : null,
            'end_time' => $useStartEndTime ? $endTime : null,
            'duration_minutes' => $useStartEndTime ? null : $durationMinutes,
            'description' => $this->faker->sentence(),
        ];
    }
}
