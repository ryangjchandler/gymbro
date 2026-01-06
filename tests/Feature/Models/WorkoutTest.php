<?php

use App\Actions\Workouts\CompleteWorkout;
use App\Actions\Workouts\SkipWorkout;
use App\Actions\Workouts\StartWorkout;
use App\Enums\WorkoutStatus;
use App\Models\CardioLog;
use App\Models\Exercise;
use App\Models\Workout;
use App\Models\WorkoutSet;
use App\Models\WorkoutTemplate;
use Illuminate\Support\Carbon;

it('can be created with factory', function () {
    $workout = Workout::factory()->create();

    expect($workout)->toBeInstanceOf(Workout::class)
        ->and($workout->scheduled_date)->toBeInstanceOf(Carbon::class)
        ->and($workout->status)->toBeInstanceOf(WorkoutStatus::class);
});

it('casts status to enum', function () {
    $workout = Workout::factory()->create([
        'status' => WorkoutStatus::Completed,
    ]);

    expect($workout->status)->toBe(WorkoutStatus::Completed);
});

it('casts scheduled_date to date', function () {
    $workout = Workout::factory()->create([
        'scheduled_date' => '2026-01-06',
    ]);

    expect($workout->scheduled_date)->toBeInstanceOf(Carbon::class)
        ->and($workout->scheduled_date->format('Y-m-d'))->toBe('2026-01-06');
});

it('has workout template relationship', function () {
    $template = WorkoutTemplate::factory()->create();
    $workout = Workout::factory()->create(['workout_template_id' => $template->id]);

    expect($workout->workoutTemplate->id)->toBe($template->id);
});

it('has workout sets relationship', function () {
    $workout = Workout::factory()->create();
    $set = WorkoutSet::factory()->create(['workout_id' => $workout->id]);

    expect($workout->workoutSets)->toHaveCount(1)
        ->and($workout->workoutSets->first()->id)->toBe($set->id);
});

it('has cardio logs relationship', function () {
    $workout = Workout::factory()->create();
    $cardioLog = CardioLog::factory()->create(['workout_id' => $workout->id]);

    expect($workout->cardioLogs)->toHaveCount(1)
        ->and($workout->cardioLogs->first()->id)->toBe($cardioLog->id);
});

it('can scope by date', function () {
    Workout::factory()->forDate('2026-01-06')->create();
    Workout::factory()->forDate('2026-01-07')->create();

    $workouts = Workout::forDate('2026-01-06')->get();

    expect($workouts)->toHaveCount(1);
});

it('can scope by completed status', function () {
    Workout::factory()->completed()->create();
    Workout::factory()->pending()->create();

    $workouts = Workout::completed()->get();

    expect($workouts)->toHaveCount(1)
        ->and($workouts->first()->status)->toBe(WorkoutStatus::Completed);
});

it('can scope by in progress status', function () {
    Workout::factory()->inProgress()->create();
    Workout::factory()->pending()->create();

    $workouts = Workout::inProgress()->get();

    expect($workouts)->toHaveCount(1)
        ->and($workouts->first()->status)->toBe(WorkoutStatus::InProgress);
});

it('can scope by pending status', function () {
    Workout::factory()->pending()->create();
    Workout::factory()->completed()->create();

    $workouts = Workout::pending()->get();

    expect($workouts)->toHaveCount(1)
        ->and($workouts->first()->status)->toBe(WorkoutStatus::Pending);
});

it('can start a workout', function () {
    Carbon::setTestNow('2026-01-06 10:00:00');

    $workout = Workout::factory()->pending()->create();
    app(StartWorkout::class)->handle($workout);

    expect($workout->status)->toBe(WorkoutStatus::InProgress)
        ->and($workout->started_at->toDateTimeString())->toBe('2026-01-06 10:00:00');

    Carbon::setTestNow();
});

it('can complete a workout', function () {
    Carbon::setTestNow('2026-01-06 11:00:00');

    $workout = Workout::factory()->inProgress()->create();
    app(CompleteWorkout::class)->handle($workout);

    expect($workout->status)->toBe(WorkoutStatus::Completed)
        ->and($workout->completed_at->toDateTimeString())->toBe('2026-01-06 11:00:00');

    Carbon::setTestNow();
});

it('can skip a workout', function () {
    $workout = Workout::factory()->pending()->create();
    app(SkipWorkout::class)->handle($workout);

    expect($workout->status)->toBe(WorkoutStatus::Skipped);
});

it('calculates total volume excluding warmup sets', function () {
    $workout = Workout::factory()->create();
    $exercise = Exercise::factory()->create();

    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'reps' => 10,
        'is_warmup' => false,
    ]);

    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 50,
        'reps' => 10,
        'is_warmup' => true,
    ]);

    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'reps' => 8,
        'is_warmup' => false,
    ]);

    $workout->refresh();

    // 100*10 + 100*8 = 1800 (excluding warmup of 50*10=500)
    expect($workout->total_volume)->toBe(1800.0);
});

it('calculates duration in minutes', function () {
    $workout = Workout::factory()->create([
        'started_at' => '2026-01-06 10:00:00',
        'completed_at' => '2026-01-06 11:30:00',
    ]);

    expect($workout->duration_in_minutes)->toBe(90);
});

it('returns null duration when workout is not complete', function () {
    $workout = Workout::factory()->pending()->create();

    expect($workout->duration_in_minutes)->toBeNull();
});
