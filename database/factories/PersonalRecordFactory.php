<?php

namespace Database\Factories;

use App\Enums\PersonalRecordType;
use App\Models\Exercise;
use App\Models\WorkoutSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonalRecord>
 */
class PersonalRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'exercise_id' => Exercise::factory(),
            'type' => PersonalRecordType::MaxWeight,
            'value' => fake()->randomFloat(1, 50, 150),
            'workout_set_id' => null,
            'achieved_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function maxWeight(?float $value = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PersonalRecordType::MaxWeight,
            'value' => $value ?? fake()->randomFloat(1, 50, 150),
        ]);
    }

    public function maxReps(?int $value = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PersonalRecordType::MaxReps,
            'value' => $value ?? fake()->numberBetween(10, 30),
        ]);
    }

    public function maxVolume(?float $value = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PersonalRecordType::MaxVolume,
            'value' => $value ?? fake()->randomFloat(0, 500, 2000),
        ]);
    }

    public function forWorkoutSet(WorkoutSet $workoutSet): static
    {
        return $this->state(fn (array $attributes) => [
            'workout_set_id' => $workoutSet->id,
            'exercise_id' => $workoutSet->exercise_id,
        ]);
    }
}
