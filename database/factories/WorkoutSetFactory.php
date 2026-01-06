<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutSet>
 */
class WorkoutSetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_id' => Workout::factory(),
            'exercise_id' => Exercise::factory(),
            'set_number' => 1,
            'weight_kg' => fake()->randomFloat(1, 20, 100),
            'reps' => fake()->numberBetween(6, 12),
            'is_warmup' => false,
            'notes' => null,
        ];
    }

    public function warmup(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_warmup' => true,
            'weight_kg' => fake()->randomFloat(1, 10, 40),
        ]);
    }

    public function setNumber(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'set_number' => $number,
        ]);
    }

    public function withWeight(float $weightKg): static
    {
        return $this->state(fn (array $attributes) => [
            'weight_kg' => $weightKg,
        ]);
    }

    public function withReps(int $reps): static
    {
        return $this->state(fn (array $attributes) => [
            'reps' => $reps,
        ]);
    }
}
