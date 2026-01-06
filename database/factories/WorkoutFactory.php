<?php

namespace Database\Factories;

use App\Enums\WorkoutStatus;
use App\Models\WorkoutTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workout>
 */
class WorkoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'scheduled_date' => fake()->dateTimeBetween('-1 month', '+1 week'),
            'workout_template_id' => WorkoutTemplate::factory(),
            'weekly_schedule_id' => null,
            'status' => WorkoutStatus::Pending,
            'notes' => null,
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkoutStatus::Pending,
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkoutStatus::InProgress,
            'started_at' => now(),
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        $startedAt = fake()->dateTimeBetween('-1 month', '-1 hour');

        return $this->state(fn (array $attributes) => [
            'status' => WorkoutStatus::Completed,
            'started_at' => $startedAt,
            'completed_at' => fake()->dateTimeBetween($startedAt, 'now'),
        ]);
    }

    public function skipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkoutStatus::Skipped,
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    public function forToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date' => today(),
        ]);
    }

    public function forDate($date): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date' => $date,
        ]);
    }
}
