<?php

namespace Database\Factories;

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'muscle_group' => fake()->randomElement(MuscleGroup::cases()),
            'type' => ExerciseType::Strength,
            'is_pinned' => false,
            'instructions' => null,
        ];
    }

    public function strength(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ExerciseType::Strength,
        ]);
    }

    public function cardio(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ExerciseType::Cardio,
            'muscle_group' => MuscleGroup::Cardio,
        ]);
    }

    public function timed(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ExerciseType::Timed,
        ]);
    }

    public function forMuscleGroup(MuscleGroup $muscleGroup): static
    {
        return $this->state(fn (array $attributes) => [
            'muscle_group' => $muscleGroup,
        ]);
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
        ]);
    }
}
