<?php

namespace App\Actions\WeeklySchedules;

use App\Models\WeeklySchedule;

class UpdateWeeklySchedule
{
    public function handle(WeeklySchedule $schedule, int $workoutTemplateId, bool $isActive): WeeklySchedule
    {
        $schedule->update([
            'workout_template_id' => $workoutTemplateId,
            'is_active' => $isActive,
        ]);

        return $schedule;
    }
}
