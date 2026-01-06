<?php

namespace App\Actions\Workouts;

use App\Enums\WorkoutStatus;
use App\Models\Workout;

class SkipWorkout
{
    public function handle(Workout $workout): void
    {
        $workout->update([
            'status' => WorkoutStatus::Skipped,
        ]);
    }
}
