<?php

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use App\Models\CardioLog;
use App\Models\Exercise;
use App\Models\PersonalRecord;
use App\Models\WorkoutSet;
use App\Models\WorkoutTemplateExercise;

it('can be created with factory', function () {
    $exercise = Exercise::factory()->create();

    expect($exercise)->toBeInstanceOf(Exercise::class)
        ->and($exercise->name)->toBeString()
        ->and($exercise->muscle_group)->toBeInstanceOf(MuscleGroup::class)
        ->and($exercise->type)->toBeInstanceOf(ExerciseType::class);
});

it('casts muscle_group to enum', function () {
    $exercise = Exercise::factory()->create([
        'muscle_group' => MuscleGroup::Chest,
    ]);

    expect($exercise->muscle_group)->toBe(MuscleGroup::Chest);
});

it('casts type to enum', function () {
    $exercise = Exercise::factory()->create([
        'type' => ExerciseType::Strength,
    ]);

    expect($exercise->type)->toBe(ExerciseType::Strength);
});

it('has workout sets relationship', function () {
    $exercise = Exercise::factory()->create();
    $workoutSet = WorkoutSet::factory()->create(['exercise_id' => $exercise->id]);

    expect($exercise->workoutSets)->toHaveCount(1)
        ->and($exercise->workoutSets->first()->id)->toBe($workoutSet->id);
});

it('has cardio logs relationship', function () {
    $exercise = Exercise::factory()->cardio()->create();
    $cardioLog = CardioLog::factory()->create(['exercise_id' => $exercise->id]);

    expect($exercise->cardioLogs)->toHaveCount(1)
        ->and($exercise->cardioLogs->first()->id)->toBe($cardioLog->id);
});

it('has personal records relationship', function () {
    $exercise = Exercise::factory()->create();
    $pr = PersonalRecord::factory()->create(['exercise_id' => $exercise->id]);

    expect($exercise->personalRecords)->toHaveCount(1)
        ->and($exercise->personalRecords->first()->id)->toBe($pr->id);
});

it('has workout template exercises relationship', function () {
    $exercise = Exercise::factory()->create();
    $templateExercise = WorkoutTemplateExercise::factory()->create(['exercise_id' => $exercise->id]);

    expect($exercise->workoutTemplateExercises)->toHaveCount(1)
        ->and($exercise->workoutTemplateExercises->first()->id)->toBe($templateExercise->id);
});

it('can determine if it is a strength exercise', function () {
    $strengthExercise = Exercise::factory()->strength()->create();
    $cardioExercise = Exercise::factory()->cardio()->create();

    expect($strengthExercise->isStrength())->toBeTrue()
        ->and($cardioExercise->isStrength())->toBeFalse();
});

it('can determine if it is a cardio exercise', function () {
    $cardioExercise = Exercise::factory()->cardio()->create();
    $strengthExercise = Exercise::factory()->strength()->create();

    expect($cardioExercise->isCardio())->toBeTrue()
        ->and($strengthExercise->isCardio())->toBeFalse();
});

it('can determine if it is a timed exercise', function () {
    $timedExercise = Exercise::factory()->timed()->create();
    $strengthExercise = Exercise::factory()->strength()->create();

    expect($timedExercise->isTimed())->toBeTrue()
        ->and($strengthExercise->isTimed())->toBeFalse();
});

it('can create exercise for specific muscle group', function () {
    $exercise = Exercise::factory()->forMuscleGroup(MuscleGroup::Back)->create();

    expect($exercise->muscle_group)->toBe(MuscleGroup::Back);
});
