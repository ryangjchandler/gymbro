<?php

namespace Database\Factories;

use App\Models\WorkoutTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklySchedule>
 */
class WeeklyScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'day_of_week' => fake()->numberBetween(0, 6),
            'workout_template_id' => WorkoutTemplate::factory(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function forDay(int $dayOfWeek): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => $dayOfWeek,
        ]);
    }
}
