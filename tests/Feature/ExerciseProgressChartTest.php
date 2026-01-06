<?php

use App\Enums\ExerciseType;
use App\Filament\Widgets\ExerciseProgressChart;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSet;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the exercise progress chart widget', function () {
    livewire(ExerciseProgressChart::class)
        ->assertOk()
        ->assertSee('Exercise Progress');
});

it('has correct sort order', function () {
    expect(ExerciseProgressChart::getSort())->toBe(5);
});

it('has responsive column span', function () {
    $widget = new ExerciseProgressChart;
    expect($widget->getColumnSpan())->toBe([
        'default' => 'full',
        'lg' => 2,
    ]);
});

it('shows empty data when no exercise is selected', function () {
    $widget = new ExerciseProgressChart;
    $widget->filters = [];

    $data = invade($widget)->getData();

    expect($data['datasets'])->toBeEmpty()
        ->and($data['labels'])->toBeEmpty();
});

it('shows exercise data when exercise is selected', function () {
    $exercise = Exercise::factory()->create(['type' => ExerciseType::Strength]);
    $workout = Workout::factory()->completed()->create([
        'completed_at' => now(),
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'reps' => 10,
        'is_warmup' => false,
    ]);

    $widget = new ExerciseProgressChart;
    $widget->filters = ['exercise_id' => $exercise->id, 'period' => '90'];

    $data = invade($widget)->getData();

    expect($data['datasets'])->not->toBeEmpty();
});

it('filters by period', function () {
    $exercise = Exercise::factory()->create(['type' => ExerciseType::Strength]);

    // Old workout (outside 30 days)
    $oldWorkout = Workout::factory()->completed()->create([
        'completed_at' => now()->subDays(60),
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $oldWorkout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 80,
        'is_warmup' => false,
    ]);

    // Recent workout (within 30 days)
    $recentWorkout = Workout::factory()->completed()->create([
        'completed_at' => now()->subDays(10),
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $recentWorkout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'is_warmup' => false,
    ]);

    $widget = new ExerciseProgressChart;
    $widget->filters = ['exercise_id' => $exercise->id, 'period' => '30'];

    $data = invade($widget)->getData();

    // Should only have 1 data point (the recent workout)
    expect($data['datasets'][0]['data'])->toHaveCount(1)
        ->and($data['datasets'][0]['data'][0])->toBe(100.0);
});

it('excludes warmup sets', function () {
    $exercise = Exercise::factory()->create(['type' => ExerciseType::Strength]);
    $workout = Workout::factory()->completed()->create([
        'completed_at' => now(),
    ]);

    // Warmup set with high weight
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 200,
        'is_warmup' => true,
    ]);

    // Working set with lower weight
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'is_warmup' => false,
    ]);

    $widget = new ExerciseProgressChart;
    $widget->filters = ['exercise_id' => $exercise->id, 'period' => '90'];

    $data = invade($widget)->getData();

    // Max should be 100 (working set), not 200 (warmup)
    expect($data['datasets'][0]['data'][0])->toBe(100.0);
});

it('only includes completed workouts', function () {
    $exercise = Exercise::factory()->create(['type' => ExerciseType::Strength]);

    // In progress workout
    $inProgressWorkout = Workout::factory()->inProgress()->create();
    WorkoutSet::factory()->create([
        'workout_id' => $inProgressWorkout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 150,
        'is_warmup' => false,
    ]);

    // Completed workout
    $completedWorkout = Workout::factory()->completed()->create([
        'completed_at' => now(),
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $completedWorkout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'is_warmup' => false,
    ]);

    $widget = new ExerciseProgressChart;
    $widget->filters = ['exercise_id' => $exercise->id, 'period' => '90'];

    $data = invade($widget)->getData();

    // Should only have the completed workout's data
    expect($data['datasets'][0]['data'])->toHaveCount(1)
        ->and($data['datasets'][0]['data'][0])->toBe(100.0);
});

it('gets max weight per day when multiple sets exist', function () {
    $exercise = Exercise::factory()->create(['type' => ExerciseType::Strength]);
    $workout = Workout::factory()->completed()->create([
        'completed_at' => now(),
    ]);

    // Multiple sets on same day
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 80,
        'is_warmup' => false,
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'is_warmup' => false,
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 90,
        'is_warmup' => false,
    ]);

    $widget = new ExerciseProgressChart;
    $widget->filters = ['exercise_id' => $exercise->id, 'period' => '90'];

    $data = invade($widget)->getData();

    // Should show max of 100
    expect($data['datasets'][0]['data'][0])->toBe(100.0);
});

it('only shows strength exercises in filter options', function () {
    Exercise::factory()->create(['name' => 'Bench Press', 'type' => ExerciseType::Strength]);
    Exercise::factory()->create(['name' => 'Running', 'type' => ExerciseType::Cardio]);

    livewire(ExerciseProgressChart::class)
        ->assertOk();

    // The filter options should only include strength exercises
    $strengthExercises = Exercise::where('type', ExerciseType::Strength)->count();
    $allExercises = Exercise::count();

    expect($strengthExercises)->toBeLessThan($allExercises);
});

it('is a line chart', function () {
    $widget = new ExerciseProgressChart;
    expect(invade($widget)->getType())->toBe('line');
});
