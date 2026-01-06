<?php

use App\Actions\Workouts\StartWorkout;
use App\Enums\WorkoutStatus;
use App\Models\Workout;
use Carbon\Carbon;

it('sets workout status to in progress', function () {
    $workout = Workout::factory()->create(['status' => WorkoutStatus::Pending]);

    app(StartWorkout::class)->handle($workout);

    expect($workout->refresh()->status)->toBe(WorkoutStatus::InProgress);
});

it('sets started_at to current time', function () {
    Carbon::setTestNow('2026-01-06 10:00:00');

    $workout = Workout::factory()->create([
        'status' => WorkoutStatus::Pending,
        'started_at' => null,
    ]);

    app(StartWorkout::class)->handle($workout);

    expect($workout->refresh()->started_at->toDateTimeString())->toBe('2026-01-06 10:00:00');

    Carbon::setTestNow();
});

it('can start a pending workout', function () {
    $workout = Workout::factory()->create(['status' => WorkoutStatus::Pending]);

    app(StartWorkout::class)->handle($workout);

    expect($workout->refresh())
        ->status->toBe(WorkoutStatus::InProgress)
        ->started_at->not->toBeNull();
});
