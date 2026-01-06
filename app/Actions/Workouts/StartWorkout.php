<?php

namespace App\Actions\Workouts;

use App\Enums\WorkoutStatus;
use App\Models\Workout;

class StartWorkout
{
    public function handle(Workout $workout): void
    {
        $workout->update([
            'status' => WorkoutStatus::InProgress,
            'started_at' => now(),
        ]);
    }
}
