<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CardioLog>
 */
class CardioLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_id' => Workout::factory(),
            'exercise_id' => Exercise::factory()->cardio(),
            'duration_seconds' => fake()->randomElement([300, 600, 900, 1200, 1800]),
            'speed' => fake()->randomFloat(1, 5, 15),
            'distance_km' => fake()->randomFloat(2, 1, 10),
            'notes' => null,
        ];
    }

    public function withDuration(int $seconds): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_seconds' => $seconds,
        ]);
    }
}
