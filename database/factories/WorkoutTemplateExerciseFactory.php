<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\WorkoutTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutTemplateExercise>
 */
class WorkoutTemplateExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_template_id' => WorkoutTemplate::factory(),
            'exercise_id' => Exercise::factory(),
            'order' => 0,
            'target_sets' => fake()->numberBetween(3, 5),
            'target_reps' => fake()->numberBetween(8, 12),
            'target_duration_seconds' => null,
            'rest_seconds' => 45,
            'notes' => null,
        ];
    }

    public function forCardio(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_reps' => null,
            'target_duration_seconds' => fake()->randomElement([300, 600, 900, 1200, 1800]),
        ]);
    }

    public function withOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order' => $order,
        ]);
    }
}
