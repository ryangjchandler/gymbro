<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BodyWeight>
 */
class BodyWeightFactory extends Factory
{
    public function definition(): array
    {
        return [
            'recorded_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'stones' => fake()->numberBetween(8, 18),
            'pounds' => fake()->randomFloat(1, 0, 13.9),
            'notes' => null,
            'photo_path' => null,
        ];
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'recorded_at' => today(),
        ]);
    }

    public function withPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => 'body-weights/'.fake()->uuid().'.jpg',
        ]);
    }
}
