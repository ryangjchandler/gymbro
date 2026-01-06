<?php

use App\Enums\WorkoutStatus;
use App\Filament\Resources\Workouts\Pages\CreateWorkout;
use App\Filament\Resources\Workouts\Pages\EditWorkout;
use App\Filament\Resources\Workouts\Pages\ListWorkouts;
use App\Filament\Resources\Workouts\Pages\ViewWorkout;
use App\Filament\Resources\Workouts\WorkoutResource;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutTemplate;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can render the list page', function () {
    $workouts = Workout::factory()->count(3)->create();

    livewire(ListWorkouts::class)
        ->assertOk()
        ->assertCanSeeTableRecords($workouts);
});

it('can render the create page', function () {
    livewire(CreateWorkout::class)
        ->assertOk();
});

it('can create a workout', function () {
    $template = WorkoutTemplate::factory()->create();

    livewire(CreateWorkout::class)
        ->fillForm([
            'scheduled_date' => '2026-01-15',
            'workout_template_id' => $template->id,
            'status' => WorkoutStatus::Pending,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Workout::where('workout_template_id', $template->id)
        ->whereDate('scheduled_date', '2026-01-15')
        ->where('status', WorkoutStatus::Pending)
        ->exists())->toBeTrue();
});

it('can create a workout with notes', function () {
    $template = WorkoutTemplate::factory()->create();

    livewire(CreateWorkout::class)
        ->fillForm([
            'scheduled_date' => '2026-01-15',
            'workout_template_id' => $template->id,
            'status' => WorkoutStatus::Pending,
            'notes' => 'Feeling strong today!',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Workout::whereDate('scheduled_date', '2026-01-15')
        ->where('notes', 'Feeling strong today!')
        ->exists())->toBeTrue();
});

it('validates required fields when creating', function () {
    livewire(CreateWorkout::class)
        ->fillForm([
            'scheduled_date' => null,
            'workout_template_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'scheduled_date' => 'required',
            'workout_template_id' => 'required',
        ]);
});

it('can render the view page', function () {
    $workout = Workout::factory()->create();

    livewire(ViewWorkout::class, ['record' => $workout->getRouteKey()])
        ->assertOk();
});

it('can render the edit page', function () {
    $workout = Workout::factory()->create();

    livewire(EditWorkout::class, ['record' => $workout->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet([
            'workout_template_id' => $workout->workout_template_id,
        ]);
});

it('can update a workout', function () {
    $workout = Workout::factory()->pending()->create();
    $newTemplate = WorkoutTemplate::factory()->create();

    livewire(EditWorkout::class, ['record' => $workout->getRouteKey()])
        ->fillForm([
            'scheduled_date' => '2026-02-01',
            'workout_template_id' => $newTemplate->id,
            'status' => WorkoutStatus::Completed,
            'notes' => 'Great workout!',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Workout::where('id', $workout->id)
        ->whereDate('scheduled_date', '2026-02-01')
        ->where('workout_template_id', $newTemplate->id)
        ->where('status', WorkoutStatus::Completed)
        ->where('notes', 'Great workout!')
        ->exists())->toBeTrue();
});

it('can delete a workout from the edit page', function () {
    $workout = Workout::factory()->create();

    livewire(EditWorkout::class, ['record' => $workout->getRouteKey()])
        ->callAction(DeleteAction::class);

    expect(Workout::find($workout->id))->toBeNull();
});

it('can search workouts by workout template name', function () {
    $pushTemplate = WorkoutTemplate::factory()->create(['name' => 'Push Day']);
    $pullTemplate = WorkoutTemplate::factory()->create(['name' => 'Pull Day']);

    $matchingWorkout = Workout::factory()->create(['workout_template_id' => $pushTemplate->id]);
    $nonMatchingWorkout = Workout::factory()->create(['workout_template_id' => $pullTemplate->id]);

    livewire(ListWorkouts::class)
        ->searchTable('Push')
        ->assertCanSeeTableRecords([$matchingWorkout])
        ->assertCanNotSeeTableRecords([$nonMatchingWorkout]);
});

it('can sort workouts by scheduled date', function () {
    Workout::factory()->forDate('2026-01-10')->create();
    Workout::factory()->forDate('2026-01-05')->create();
    Workout::factory()->forDate('2026-01-15')->create();

    livewire(ListWorkouts::class)
        ->sortTable('scheduled_date')
        ->assertCanSeeTableRecords(Workout::query()->orderBy('scheduled_date')->get(), inOrder: true)
        ->sortTable('scheduled_date', 'desc')
        ->assertCanSeeTableRecords(Workout::query()->orderBy('scheduled_date', 'desc')->get(), inOrder: true);
});

it('defaults to sorting by scheduled date descending', function () {
    Workout::factory()->forDate('2026-01-10')->create();
    Workout::factory()->forDate('2026-01-05')->create();
    Workout::factory()->forDate('2026-01-15')->create();

    livewire(ListWorkouts::class)
        ->assertCanSeeTableRecords(Workout::query()->orderBy('scheduled_date', 'desc')->get(), inOrder: true);
});

it('can filter workouts by status', function () {
    $pendingWorkout = Workout::factory()->pending()->create();
    $completedWorkout = Workout::factory()->completed()->create();
    $inProgressWorkout = Workout::factory()->inProgress()->create();

    livewire(ListWorkouts::class)
        ->filterTable('status', WorkoutStatus::Pending->value)
        ->assertCanSeeTableRecords([$pendingWorkout])
        ->assertCanNotSeeTableRecords([$completedWorkout, $inProgressWorkout]);
});

it('can bulk delete workouts', function () {
    $workouts = Workout::factory()->count(3)->create();

    livewire(ListWorkouts::class)
        ->assertCanSeeTableRecords($workouts)
        ->selectTableRecords($workouts)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertCanNotSeeTableRecords($workouts);

    $workouts->each(fn (Workout $workout) => expect(Workout::find($workout->id))->toBeNull());
});

it('has correct navigation icon', function () {
    expect(WorkoutResource::getNavigationIcon())->not->toBeNull();
});

it('can create a workout with different statuses', function () {
    $template = WorkoutTemplate::factory()->create();

    livewire(CreateWorkout::class)
        ->fillForm([
            'scheduled_date' => '2026-01-20',
            'workout_template_id' => $template->id,
            'status' => WorkoutStatus::InProgress,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Workout::whereDate('scheduled_date', '2026-01-20')
        ->where('status', WorkoutStatus::InProgress)
        ->exists())->toBeTrue();
});

it('displays status as badge in table', function () {
    Workout::factory()->pending()->create();
    Workout::factory()->completed()->create();

    livewire(ListWorkouts::class)
        ->assertOk();
});

it('can start a pending workout from the table', function () {
    $workout = Workout::factory()->pending()->create();

    livewire(ListWorkouts::class)
        ->assertTableActionVisible('start', $workout)
        ->callTableAction('start', $workout);

    expect($workout->refresh())
        ->status->toBe(WorkoutStatus::InProgress)
        ->started_at->not->toBeNull();
});

it('cannot start an already in-progress workout', function () {
    $workout = Workout::factory()->inProgress()->create();

    livewire(ListWorkouts::class)
        ->assertTableActionHidden('start', $workout);
});

it('cannot start a completed workout', function () {
    $workout = Workout::factory()->completed()->create();

    livewire(ListWorkouts::class)
        ->assertTableActionHidden('start', $workout);
});

it('cannot start a skipped workout', function () {
    $workout = Workout::factory()->skipped()->create();

    livewire(ListWorkouts::class)
        ->assertTableActionHidden('start', $workout);
});

it('can start a workout from the view page', function () {
    $workout = Workout::factory()->pending()->create();

    livewire(ViewWorkout::class, ['record' => $workout->getRouteKey()])
        ->assertActionVisible('start')
        ->callAction('start');

    expect($workout->refresh())
        ->status->toBe(WorkoutStatus::InProgress)
        ->started_at->not->toBeNull();
});

it('hides start action on view page for in-progress workout', function () {
    $workout = Workout::factory()->inProgress()->create();

    livewire(ViewWorkout::class, ['record' => $workout->getRouteKey()])
        ->assertActionHidden('start');
});

it('hides start action on view page for completed workout', function () {
    $workout = Workout::factory()->completed()->create();

    livewire(ViewWorkout::class, ['record' => $workout->getRouteKey()])
        ->assertActionHidden('start');
});
