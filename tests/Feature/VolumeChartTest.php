<?php

use App\Filament\Widgets\VolumeChart;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSet;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the volume chart widget', function () {
    livewire(VolumeChart::class)
        ->assertOk()
        ->assertSee('Training Volume Over Time');
});

it('has correct sort order', function () {
    expect(VolumeChart::getSort())->toBe(7);
});

it('has responsive column span', function () {
    $widget = new VolumeChart;
    expect($widget->getColumnSpan())->toBe([
        'default' => 'full',
        'lg' => 2,
    ]);
});

it('shows empty data when no workouts exist', function () {
    $widget = new VolumeChart;
    $widget->filters = ['period' => '90', 'grouping' => 'day'];

    $data = invade($widget)->getData();

    expect($data['datasets'])->toBeEmpty()
        ->and($data['labels'])->toBeEmpty();
});

it('shows volume data when workouts exist', function () {
    $exercise = Exercise::factory()->create();
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

    $widget = new VolumeChart;
    $widget->filters = ['period' => '90', 'grouping' => 'day'];

    $data = invade($widget)->getData();

    expect($data['datasets'])->not->toBeEmpty();
});

it('calculates volume correctly', function () {
    $exercise = Exercise::factory()->create();
    $workout = Workout::factory()->completed()->create([
        'completed_at' => now(),
    ]);

    // 100kg x 10 reps = 1000kg volume
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'reps' => 10,
        'is_warmup' => false,
    ]);

    // 80kg x 8 reps = 640kg volume
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 80,
        'reps' => 8,
        'is_warmup' => false,
    ]);

    $widget = new VolumeChart;
    $widget->filters = ['period' => '90', 'grouping' => 'day'];

    $data = invade($widget)->getData();

    // Total: 1000 + 640 = 1640
    expect($data['datasets'][0]['data'][0])->toBe(1640.0);
});

it('excludes warmup sets from volume calculation', function () {
    $exercise = Exercise::factory()->create();
    $workout = Workout::factory()->completed()->create([
        'completed_at' => now(),
    ]);

    // Working set: 100kg x 10 reps = 1000kg
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'reps' => 10,
        'is_warmup' => false,
    ]);

    // Warmup set (should be excluded): 50kg x 10 reps = 500kg
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 50,
        'reps' => 10,
        'is_warmup' => true,
    ]);

    $widget = new VolumeChart;
    $widget->filters = ['period' => '90', 'grouping' => 'day'];

    $data = invade($widget)->getData();

    // Should only count working set: 1000kg
    expect($data['datasets'][0]['data'][0])->toBe(1000.0);
});

it('only includes completed workouts', function () {
    $exercise = Exercise::factory()->create();

    // In progress workout (should be excluded)
    $inProgressWorkout = Workout::factory()->inProgress()->create();
    WorkoutSet::factory()->create([
        'workout_id' => $inProgressWorkout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 200,
        'reps' => 10,
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
        'reps' => 10,
        'is_warmup' => false,
    ]);

    $widget = new VolumeChart;
    $widget->filters = ['period' => '90', 'grouping' => 'day'];

    $data = invade($widget)->getData();

    // Should only show 1000kg (completed workout)
    expect($data['datasets'][0]['data'][0])->toBe(1000.0);
});

it('filters by period', function () {
    $exercise = Exercise::factory()->create();

    // Old workout (outside 30 days)
    $oldWorkout = Workout::factory()->completed()->create([
        'completed_at' => now()->subDays(60),
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $oldWorkout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'reps' => 10,
        'is_warmup' => false,
    ]);

    // Recent workout (within 30 days)
    $recentWorkout = Workout::factory()->completed()->create([
        'completed_at' => now()->subDays(10),
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $recentWorkout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 50,
        'reps' => 10,
        'is_warmup' => false,
    ]);

    $widget = new VolumeChart;
    $widget->filters = ['period' => '30', 'grouping' => 'day'];

    $data = invade($widget)->getData();

    // Should only have recent workout volume: 500kg
    expect($data['datasets'][0]['data'])->toHaveCount(1)
        ->and($data['datasets'][0]['data'][0])->toBe(500.0);
});

it('groups by week correctly', function () {
    $exercise = Exercise::factory()->create();

    // Create workouts on different days of the same week
    $monday = now()->startOfWeek();

    $workout1 = Workout::factory()->completed()->create([
        'completed_at' => $monday,
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $workout1->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'reps' => 10,
        'is_warmup' => false,
    ]);

    $workout2 = Workout::factory()->completed()->create([
        'completed_at' => $monday->copy()->addDays(2),
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $workout2->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'reps' => 10,
        'is_warmup' => false,
    ]);

    $widget = new VolumeChart;
    $widget->filters = ['period' => '90', 'grouping' => 'week'];

    $data = invade($widget)->getData();

    // Both workouts should be grouped into one week
    expect($data['datasets'][0]['data'])->toHaveCount(1)
        ->and($data['datasets'][0]['data'][0])->toBe(2000.0);
});

it('is a bar chart', function () {
    $widget = new VolumeChart;
    expect(invade($widget)->getType())->toBe('bar');
});

it('uses week number labels when grouped by week', function () {
    $exercise = Exercise::factory()->create();
    $workout = Workout::factory()->completed()->create([
        'completed_at' => now()->startOfWeek(),
    ]);
    WorkoutSet::factory()->create([
        'workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'weight_kg' => 100,
        'reps' => 10,
        'is_warmup' => false,
    ]);

    $widget = new VolumeChart;
    $widget->filters = ['period' => '90', 'grouping' => 'week'];

    $data = invade($widget)->getData();

    expect($data['labels'][0])->toStartWith('W');
});
