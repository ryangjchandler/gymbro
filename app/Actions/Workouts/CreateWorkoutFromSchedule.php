<?php

namespace App\Actions\Workouts;

use App\Models\WeeklySchedule;
use App\Models\Workout;
use Carbon\Carbon;

class CreateWorkoutFromSchedule
{
    public function __construct(
        private StartWorkout $startWorkout,
    ) {}

    public function handle(WeeklySchedule $schedule, Carbon $date): Workout
    {
        $workout = Workout::create([
            'workout_template_id' => $schedule->workout_template_id,
            'scheduled_date' => $date,
        ]);

        $this->startWorkout->handle($workout);

        return $workout;
    }
}
