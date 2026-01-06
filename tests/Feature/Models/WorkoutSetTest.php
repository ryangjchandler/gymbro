<?php

use App\Models\Exercise;
use App\Models\PersonalRecord;
use App\Models\Workout;
use App\Models\WorkoutSet;

it('can be created with factory', function () {
    $set = WorkoutSet::factory()->create();

    expect($set)->toBeInstanceOf(WorkoutSet::class)
        ->and($set->set_number)->toBeInt()
        ->and($set->is_warmup)->toBeBool();
});

it('casts weight_kg to decimal', function () {
    $set = WorkoutSet::factory()->create([
        'weight_kg' => 62.5,
    ]);

    expect($set->weight_kg)->toBe('62.50');
});

it('casts reps to integer', function () {
    $set = WorkoutSet::factory()->create([
        'reps' => 10,
    ]);

    expect($set->reps)->toBe(10);
});

it('casts is_warmup to boolean', function () {
    $set = WorkoutSet::factory()->create([
        'is_warmup' => 1,
    ]);

    expect($set->is_warmup)->toBeTrue();
});

it('has workout relationship', function () {
    $workout = Workout::factory()->create();
    $set = WorkoutSet::factory()->create(['workout_id' => $workout->id]);

    expect($set->workout->id)->toBe($workout->id);
});

it('has exercise relationship', function () {
    $exercise = Exercise::factory()->create();
    $set = WorkoutSet::factory()->create(['exercise_id' => $exercise->id]);

    expect($set->exercise->id)->toBe($exercise->id);
});

it('has personal record relationship', function () {
    $set = WorkoutSet::factory()->create();
    $pr = PersonalRecord::factory()->create(['workout_set_id' => $set->id]);

    expect($set->personalRecord->id)->toBe($pr->id);
});

it('calculates volume', function () {
    $set = WorkoutSet::factory()->create([
        'weight_kg' => 100,
        'reps' => 10,
    ]);

    expect($set->volume)->toBe(1000.0);
});

it('returns zero volume when weight is null', function () {
    $set = WorkoutSet::factory()->create([
        'weight_kg' => null,
        'reps' => 10,
    ]);

    expect($set->volume)->toEqual(0);
});

it('returns zero volume when reps is null', function () {
    $set = WorkoutSet::factory()->create([
        'weight_kg' => 100,
        'reps' => null,
    ]);

    expect($set->volume)->toEqual(0);
});

it('can create warmup set using factory state', function () {
    $set = WorkoutSet::factory()->warmup()->create();

    expect($set->is_warmup)->toBeTrue();
});

it('can create set with specific set number using factory state', function () {
    $set = WorkoutSet::factory()->setNumber(3)->create();

    expect($set->set_number)->toBe(3);
});

it('can create set with specific weight using factory state', function () {
    $set = WorkoutSet::factory()->withWeight(80.5)->create();

    expect($set->weight_kg)->toBe('80.50');
});

it('can create set with specific reps using factory state', function () {
    $set = WorkoutSet::factory()->withReps(12)->create();

    expect($set->reps)->toBe(12);
});
