<?php

namespace App\Actions\Workouts;

use App\Enums\WorkoutStatus;
use App\Models\Workout;
use App\Services\AchievementService;

class CompleteWorkout
{
    public function __construct(
        protected AchievementService $achievementService,
    ) {}

    public function handle(Workout $workout): void
    {
        $workout->update([
            'status' => WorkoutStatus::Completed,
            'completed_at' => now(),
        ]);

        $this->achievementService->checkAndUnlockAll();
    }
}
