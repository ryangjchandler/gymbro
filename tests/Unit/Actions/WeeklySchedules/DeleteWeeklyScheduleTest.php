<?php

use App\Actions\WeeklySchedules\DeleteWeeklySchedule;
use App\Models\WeeklySchedule;

it('deletes a weekly schedule', function () {
    $schedule = WeeklySchedule::factory()->create();
    $scheduleId = $schedule->id;

    app(DeleteWeeklySchedule::class)->handle($schedule);

    expect(WeeklySchedule::find($scheduleId))->toBeNull();
});

it('returns void', function () {
    $schedule = WeeklySchedule::factory()->create();

    $result = app(DeleteWeeklySchedule::class)->handle($schedule);

    expect($result)->toBeNull();
});
