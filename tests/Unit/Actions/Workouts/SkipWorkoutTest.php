<?php

use App\Actions\Workouts\SkipWorkout;
use App\Enums\WorkoutStatus;
use App\Models\Workout;

it('sets workout status to skipped', function () {
    $workout = Workout::factory()->create(['status' => WorkoutStatus::InProgress]);

    app(SkipWorkout::class)->handle($workout);

    expect($workout->refresh()->status)->toBe(WorkoutStatus::Skipped);
});

it('can skip a pending workout', function () {
    $workout = Workout::factory()->create(['status' => WorkoutStatus::Pending]);

    app(SkipWorkout::class)->handle($workout);

    expect($workout->refresh()->status)->toBe(WorkoutStatus::Skipped);
});

it('can skip an in progress workout', function () {
    $workout = Workout::factory()->create(['status' => WorkoutStatus::InProgress]);

    app(SkipWorkout::class)->handle($workout);

    expect($workout->refresh()->status)->toBe(WorkoutStatus::Skipped);
});
