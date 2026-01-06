<?php

namespace App\Actions\WeeklySchedules;

use App\Models\WeeklySchedule;

class CreateWeeklySchedule
{
    public function handle(int $dayOfWeek, int $workoutTemplateId, bool $isActive = true): WeeklySchedule
    {
        return WeeklySchedule::create([
            'day_of_week' => $dayOfWeek,
            'workout_template_id' => $workoutTemplateId,
            'is_active' => $isActive,
        ]);
    }
}
