<?php

use App\Models\Exercise;
use App\Models\WeeklySchedule;
use App\Models\Workout;
use App\Models\WorkoutTemplate;
use App\Models\WorkoutTemplateExercise;

it('can be created with factory', function () {
    $template = WorkoutTemplate::factory()->create();

    expect($template)->toBeInstanceOf(WorkoutTemplate::class)
        ->and($template->name)->toBeString();
});

it('has workout template exercises relationship ordered by order column', function () {
    $template = WorkoutTemplate::factory()->create();
    $exercise1 = Exercise::factory()->create();
    $exercise2 = Exercise::factory()->create();

    WorkoutTemplateExercise::factory()->create([
        'workout_template_id' => $template->id,
        'exercise_id' => $exercise2->id,
        'order' => 2,
    ]);

    WorkoutTemplateExercise::factory()->create([
        'workout_template_id' => $template->id,
        'exercise_id' => $exercise1->id,
        'order' => 1,
    ]);

    $template->refresh();

    expect($template->workoutTemplateExercises)->toHaveCount(2)
        ->and($template->workoutTemplateExercises->first()->exercise_id)->toBe($exercise1->id)
        ->and($template->workoutTemplateExercises->last()->exercise_id)->toBe($exercise2->id);
});

it('has exercises relationship through pivot', function () {
    $template = WorkoutTemplate::factory()->create();
    $exercise = Exercise::factory()->create();

    WorkoutTemplateExercise::factory()->create([
        'workout_template_id' => $template->id,
        'exercise_id' => $exercise->id,
        'target_sets' => 4,
        'target_reps' => 10,
        'rest_seconds' => 60,
    ]);

    $template->refresh();

    expect($template->exercises)->toHaveCount(1)
        ->and($template->exercises->first()->pivot->target_sets)->toBe(4)
        ->and($template->exercises->first()->pivot->target_reps)->toBe(10)
        ->and($template->exercises->first()->pivot->rest_seconds)->toBe(60);
});

it('has weekly schedules relationship', function () {
    $template = WorkoutTemplate::factory()->create();
    $schedule = WeeklySchedule::factory()->create(['workout_template_id' => $template->id]);

    expect($template->weeklySchedules)->toHaveCount(1)
        ->and($template->weeklySchedules->first()->id)->toBe($schedule->id);
});

it('has workouts relationship', function () {
    $template = WorkoutTemplate::factory()->create();
    $workout = Workout::factory()->create(['workout_template_id' => $template->id]);

    expect($template->workouts)->toHaveCount(1)
        ->and($template->workouts->first()->id)->toBe($workout->id);
});
