<?php

namespace App\Actions\WeeklySchedules;

use App\Models\WeeklySchedule;

class DeleteWeeklySchedule
{
    public function handle(WeeklySchedule $schedule): void
    {
        $schedule->delete();
    }
}
