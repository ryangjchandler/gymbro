<?php

use App\Actions\WeeklySchedules\CreateWeeklySchedule;
use App\Models\WeeklySchedule;
use App\Models\WorkoutTemplate;

it('creates a weekly schedule', function () {
    $template = WorkoutTemplate::factory()->create();

    $schedule = app(CreateWeeklySchedule::class)->handle(
        dayOfWeek: 1,
        workoutTemplateId: $template->id,
    );

    expect($schedule)
        ->toBeInstanceOf(WeeklySchedule::class)
        ->day_of_week->toBe(1)
        ->workout_template_id->toBe($template->id)
        ->is_active->toBeTrue();
});

it('creates an active schedule by default', function () {
    $template = WorkoutTemplate::factory()->create();

    $schedule = app(CreateWeeklySchedule::class)->handle(
        dayOfWeek: 3,
        workoutTemplateId: $template->id,
    );

    expect($schedule->is_active)->toBeTrue();
});

it('can create an inactive schedule', function () {
    $template = WorkoutTemplate::factory()->create();

    $schedule = app(CreateWeeklySchedule::class)->handle(
        dayOfWeek: 5,
        workoutTemplateId: $template->id,
        isActive: false,
    );

    expect($schedule->is_active)->toBeFalse();
});

it('persists the schedule to the database', function () {
    $template = WorkoutTemplate::factory()->create();

    $schedule = app(CreateWeeklySchedule::class)->handle(
        dayOfWeek: 2,
        workoutTemplateId: $template->id,
    );

    expect(WeeklySchedule::where('id', $schedule->id)->exists())->toBeTrue();
});
