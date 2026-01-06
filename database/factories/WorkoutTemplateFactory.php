<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutTemplate>
 */
class WorkoutTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Push Day', 'Pull Day', 'Leg Day', 'Upper Body', 'Lower Body', 'Full Body']),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
