<?php

use App\Actions\Workouts\CreateWorkoutFromSchedule;
use App\Enums\WorkoutStatus;
use App\Models\WeeklySchedule;
use App\Models\Workout;
use Carbon\Carbon;

it('creates a workout from a weekly schedule', function () {
    $schedule = WeeklySchedule::factory()->create();
    $date = Carbon::parse('2026-01-06');

    $workout = app(CreateWorkoutFromSchedule::class)->handle($schedule, $date);

    expect($workout)
        ->toBeInstanceOf(Workout::class)
        ->workout_template_id->toBe($schedule->workout_template_id)
        ->scheduled_date->toDateString()->toBe('2026-01-06');
});

it('starts the workout after creation', function () {
    $schedule = WeeklySchedule::factory()->create();
    $date = Carbon::parse('2026-01-06');

    $workout = app(CreateWorkoutFromSchedule::class)->handle($schedule, $date);

    expect($workout)
        ->status->toBe(WorkoutStatus::InProgress)
        ->started_at->not->toBeNull();
});

it('uses the correct workout template from the schedule', function () {
    $schedule = WeeklySchedule::factory()->create();
    $date = Carbon::parse('2026-01-06');

    $workout = app(CreateWorkoutFromSchedule::class)->handle($schedule, $date);

    expect($workout->workout_template_id)->toBe($schedule->workout_template_id);
});

it('persists the workout to the database', function () {
    $schedule = WeeklySchedule::factory()->create();
    $date = Carbon::parse('2026-01-06');

    $workout = app(CreateWorkoutFromSchedule::class)->handle($schedule, $date);

    expect(Workout::where('id', $workout->id)->exists())->toBeTrue();
    expect($workout->fresh())
        ->workout_template_id->toBe($schedule->workout_template_id)
        ->scheduled_date->toDateString()->toBe('2026-01-06')
        ->status->toBe(WorkoutStatus::InProgress);
});
