<?php

use App\Actions\Workouts\CompleteWorkout;
use App\Enums\WorkoutStatus;
use App\Models\Workout;
use Carbon\Carbon;

it('sets workout status to completed', function () {
    $workout = Workout::factory()->create(['status' => WorkoutStatus::InProgress]);

    app(CompleteWorkout::class)->handle($workout);

    expect($workout->refresh()->status)->toBe(WorkoutStatus::Completed);
});

it('sets completed_at to current time', function () {
    Carbon::setTestNow('2026-01-06 11:30:00');

    $workout = Workout::factory()->create([
        'status' => WorkoutStatus::InProgress,
        'completed_at' => null,
    ]);

    app(CompleteWorkout::class)->handle($workout);

    expect($workout->refresh()->completed_at->toDateTimeString())->toBe('2026-01-06 11:30:00');

    Carbon::setTestNow();
});

it('can complete an in progress workout', function () {
    $workout = Workout::factory()->create(['status' => WorkoutStatus::InProgress]);

    app(CompleteWorkout::class)->handle($workout);

    expect($workout->refresh())
        ->status->toBe(WorkoutStatus::Completed)
        ->completed_at->not->toBeNull();
});
