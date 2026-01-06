<?php

use App\Actions\Workouts\CompleteWorkout;
use App\Actions\Workouts\SkipWorkout;
use App\Enums\WorkoutStatus;
use App\Filament\Widgets\ActiveWorkoutWidget;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutTemplate;
use App\Models\WorkoutTemplateExercise;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can view is false when no workout is in progress', function () {
    Workout::factory()->pending()->create();

    expect(ActiveWorkoutWidget::canView())->toBeFalse();
});

it('can view is true when a workout is in progress', function () {
    Workout::factory()->inProgress()->create();

    expect(ActiveWorkoutWidget::canView())->toBeTrue();
});

it('renders the widget when a workout is in progress', function () {
    $workout = Workout::factory()->inProgress()->create();

    livewire(ActiveWorkoutWidget::class)
        ->assertOk()
        ->assertSee('Active Workout');
});

it('displays the workout template name', function () {
    $template = WorkoutTemplate::factory()->create(['name' => 'Upper Body Blast']);
    $workout = Workout::factory()->inProgress()->for($template, 'workoutTemplate')->create();

    livewire(ActiveWorkoutWidget::class)
        ->assertOk()
        ->assertSee('Upper Body Blast');
});

it('displays the workout start time', function () {
    $workout = Workout::factory()->inProgress()->create([
        'started_at' => now()->setHour(14)->setMinute(30),
    ]);

    livewire(ActiveWorkoutWidget::class)
        ->assertOk()
        ->assertSee('2:30 PM');
});

it('displays the complete workout action', function () {
    Workout::factory()->inProgress()->create();

    livewire(ActiveWorkoutWidget::class)
        ->assertOk()
        ->assertActionVisible('completeWorkout');
});

it('displays the skip workout action', function () {
    Workout::factory()->inProgress()->create();

    livewire(ActiveWorkoutWidget::class)
        ->assertOk()
        ->assertActionVisible('skipWorkout');
});

it('can complete a workout via the action', function () {
    $workout = Workout::factory()->inProgress()->create();

    livewire(ActiveWorkoutWidget::class)
        ->callAction('completeWorkout');

    expect($workout->refresh())
        ->status->toBe(WorkoutStatus::Completed)
        ->completed_at->not->toBeNull();
});

it('can skip a workout via the action', function () {
    $workout = Workout::factory()->inProgress()->create();

    livewire(ActiveWorkoutWidget::class)
        ->callAction('skipWorkout');

    expect($workout->refresh())
        ->status->toBe(WorkoutStatus::Skipped);
});

it('hides after completing workout', function () {
    $workout = Workout::factory()->inProgress()->create();

    // Before completing
    expect(ActiveWorkoutWidget::canView())->toBeTrue();

    // Complete the workout
    app(CompleteWorkout::class)->handle($workout);

    // After completing
    expect(ActiveWorkoutWidget::canView())->toBeFalse();
});

it('hides after skipping workout', function () {
    $workout = Workout::factory()->inProgress()->create();

    // Before skipping
    expect(ActiveWorkoutWidget::canView())->toBeTrue();

    // Skip the workout
    app(SkipWorkout::class)->handle($workout);

    // After skipping
    expect(ActiveWorkoutWidget::canView())->toBeFalse();
});

it('displays exercises from the workout template', function () {
    $template = WorkoutTemplate::factory()->create();
    $exercise = WorkoutTemplateExercise::factory()->for($template, 'workoutTemplate')->create([
        'target_sets' => 4,
        'target_reps' => 10,
    ]);
    $workout = Workout::factory()->inProgress()->for($template, 'workoutTemplate')->create();

    livewire(ActiveWorkoutWidget::class)
        ->assertOk()
        ->assertSee($exercise->exercise->name)
        ->assertSee('4 sets');
});

it('displays rest seconds for exercises', function () {
    $template = WorkoutTemplate::factory()->create();
    WorkoutTemplateExercise::factory()->for($template, 'workoutTemplate')->create([
        'rest_seconds' => 90,
    ]);
    $workout = Workout::factory()->inProgress()->for($template, 'workoutTemplate')->create();

    livewire(ActiveWorkoutWidget::class)
        ->assertOk()
        ->assertSee('90s rest');
});

it('includes the rest timer component', function () {
    Workout::factory()->inProgress()->create();

    livewire(ActiveWorkoutWidget::class)
        ->assertOk()
        ->assertSeeLivewire(\App\Livewire\RestTimer::class);
});

it('loads the most recent in-progress workout', function () {
    $olderWorkout = Workout::factory()->inProgress()->create([
        'started_at' => now()->subHours(2),
    ]);
    $newerWorkout = Workout::factory()->inProgress()->create([
        'started_at' => now()->subHour(),
    ]);

    $widget = livewire(ActiveWorkoutWidget::class);

    expect($widget->get('activeWorkout')->id)->toBe($newerWorkout->id);
});

it('does not display widget for pending workouts', function () {
    Workout::factory()->pending()->create();

    expect(ActiveWorkoutWidget::canView())->toBeFalse();
});

it('does not display widget for completed workouts', function () {
    Workout::factory()->completed()->create();

    expect(ActiveWorkoutWidget::canView())->toBeFalse();
});

it('does not display widget for skipped workouts', function () {
    Workout::factory()->skipped()->create();

    expect(ActiveWorkoutWidget::canView())->toBeFalse();
});

it('has full column span', function () {
    $workout = Workout::factory()->inProgress()->create();

    $widget = new ActiveWorkoutWidget;
    $widget->mount();

    expect($widget->getColumnSpan())->toBe('full');
});

it('renders without error when started_at is null', function () {
    // Simulate a workout manually set to in_progress without using start()
    Workout::factory()->create([
        'status' => WorkoutStatus::InProgress,
        'started_at' => null,
    ]);

    livewire(ActiveWorkoutWidget::class)
        ->assertOk()
        ->assertSee('Active Workout')
        ->assertDontSee('Started');
});
