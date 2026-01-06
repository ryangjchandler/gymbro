<?php

namespace Database\Factories;

use App\Enums\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAchievement>
 */
class UserAchievementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'achievement' => fake()->randomElement(Achievement::cases()),
            'achieved_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function achievement(Achievement $achievement): static
    {
        return $this->state(fn (array $attributes) => [
            'achievement' => $achievement,
        ]);
    }

    public function achievedAt(\DateTimeInterface $date): static
    {
        return $this->state(fn (array $attributes) => [
            'achieved_at' => $date,
        ]);
    }
}
