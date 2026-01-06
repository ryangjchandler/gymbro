<?php

use App\Actions\WeeklySchedules\UpdateWeeklySchedule;
use App\Models\WeeklySchedule;
use App\Models\WorkoutTemplate;

it('updates a weekly schedule workout template', function () {
    $schedule = WeeklySchedule::factory()->create();
    $newTemplate = WorkoutTemplate::factory()->create();

    $updated = app(UpdateWeeklySchedule::class)->handle(
        schedule: $schedule,
        workoutTemplateId: $newTemplate->id,
        isActive: $schedule->is_active,
    );

    expect($updated->workout_template_id)->toBe($newTemplate->id);
});

it('updates a weekly schedule active status', function () {
    $schedule = WeeklySchedule::factory()->create(['is_active' => true]);

    $updated = app(UpdateWeeklySchedule::class)->handle(
        schedule: $schedule,
        workoutTemplateId: $schedule->workout_template_id,
        isActive: false,
    );

    expect($updated->is_active)->toBeFalse();
});

it('can activate an inactive schedule', function () {
    $schedule = WeeklySchedule::factory()->inactive()->create();

    $updated = app(UpdateWeeklySchedule::class)->handle(
        schedule: $schedule,
        workoutTemplateId: $schedule->workout_template_id,
        isActive: true,
    );

    expect($updated->is_active)->toBeTrue();
});

it('returns the updated schedule', function () {
    $schedule = WeeklySchedule::factory()->create();
    $newTemplate = WorkoutTemplate::factory()->create();

    $updated = app(UpdateWeeklySchedule::class)->handle(
        schedule: $schedule,
        workoutTemplateId: $newTemplate->id,
        isActive: true,
    );

    expect($updated)
        ->toBeInstanceOf(WeeklySchedule::class)
        ->id->toBe($schedule->id);
});

it('persists changes to the database', function () {
    $schedule = WeeklySchedule::factory()->create(['is_active' => true]);
    $newTemplate = WorkoutTemplate::factory()->create();

    app(UpdateWeeklySchedule::class)->handle(
        schedule: $schedule,
        workoutTemplateId: $newTemplate->id,
        isActive: false,
    );

    expect($schedule->fresh())
        ->workout_template_id->toBe($newTemplate->id)
        ->is_active->toBeFalse();
});
