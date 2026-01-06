<?php

namespace App\Actions\Workouts;

use App\Enums\WorkoutStatus;
use App\Models\Workout;

class CompleteWorkout
{
    public function handle(Workout $workout): void
    {
        $workout->update([
            'status' => WorkoutStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}
