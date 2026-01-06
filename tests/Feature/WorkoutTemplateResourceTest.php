<?php

use App\Filament\Resources\WorkoutTemplates\Pages\CreateWorkoutTemplate;
use App\Filament\Resources\WorkoutTemplates\Pages\EditWorkoutTemplate;
use App\Filament\Resources\WorkoutTemplates\Pages\ListWorkoutTemplates;
use App\Filament\Resources\WorkoutTemplates\Pages\ViewWorkoutTemplate;
use App\Filament\Resources\WorkoutTemplates\WorkoutTemplateResource;
use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutTemplate;
use App\Models\WorkoutTemplateExercise;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can render the list page', function () {
    $templates = WorkoutTemplate::factory()->count(5)->create();

    livewire(ListWorkoutTemplates::class)
        ->assertOk()
        ->assertCanSeeTableRecords($templates);
});

it('can render the create page', function () {
    livewire(CreateWorkoutTemplate::class)
        ->assertOk();
});

it('can create a workout template', function () {
    $undoRepeaterFake = Repeater::fake();

    livewire(CreateWorkoutTemplate::class)
        ->fillForm([
            'name' => 'Upper Body',
            'description' => 'A great upper body workout',
            'workoutTemplateExercises' => [],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    expect(WorkoutTemplate::where('name', 'Upper Body')->first())
        ->not->toBeNull()
        ->description->toBe('A great upper body workout');
});

it('can create a workout template with exercises', function () {
    $undoRepeaterFake = Repeater::fake();

    $exercises = Exercise::factory()->count(2)->create();

    livewire(CreateWorkoutTemplate::class)
        ->fillForm([
            'name' => 'Upper Body',
            'description' => 'A great upper body workout',
            'workoutTemplateExercises' => [
                [
                    'exercise_id' => $exercises[0]->id,
                    'target_sets' => 3,
                    'target_reps' => 10,
                    'rest_seconds' => 60,
                ],
                [
                    'exercise_id' => $exercises[1]->id,
                    'target_sets' => 4,
                    'target_reps' => 8,
                    'rest_seconds' => 90,
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    $template = WorkoutTemplate::where('name', 'Upper Body')->first();

    expect($template)
        ->not->toBeNull()
        ->workoutTemplateExercises->toHaveCount(2);
});

it('validates required fields when creating', function () {
    livewire(CreateWorkoutTemplate::class)
        ->fillForm([
            'name' => '',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
        ]);
});

it('can render the view page', function () {
    $template = WorkoutTemplate::factory()->create();

    livewire(ViewWorkoutTemplate::class, ['record' => $template->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => $template->name,
        ]);
});

it('can render the edit page', function () {
    $template = WorkoutTemplate::factory()->create();

    livewire(EditWorkoutTemplate::class, ['record' => $template->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => $template->name,
        ]);
});

it('can update a workout template', function () {
    $template = WorkoutTemplate::factory()->create();

    livewire(EditWorkoutTemplate::class, ['record' => $template->getRouteKey()])
        ->fillForm([
            'name' => 'Updated Template Name',
            'description' => 'Updated description',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($template->fresh())
        ->name->toBe('Updated Template Name')
        ->description->toBe('Updated description');
});

it('can add exercises to a workout template', function () {
    $undoRepeaterFake = Repeater::fake();

    $template = WorkoutTemplate::factory()->create();
    $exercise = Exercise::factory()->create();

    livewire(EditWorkoutTemplate::class, ['record' => $template->getRouteKey()])
        ->fillForm([
            'workoutTemplateExercises' => [
                [
                    'exercise_id' => $exercise->id,
                    'target_sets' => 3,
                    'target_reps' => 12,
                    'rest_seconds' => 45,
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    $templateExercise = WorkoutTemplateExercise::where('workout_template_id', $template->id)
        ->where('exercise_id', $exercise->id)
        ->first();

    expect($template->fresh()->workoutTemplateExercises)->toHaveCount(1);
    expect($templateExercise)
        ->not->toBeNull()
        ->target_sets->toBe(3)
        ->target_reps->toBe(12)
        ->rest_seconds->toBe(45);
});

it('can delete a workout template from the edit page', function () {
    $template = WorkoutTemplate::factory()->create();

    livewire(EditWorkoutTemplate::class, ['record' => $template->getRouteKey()])
        ->callAction(DeleteAction::class);

    expect(WorkoutTemplate::find($template->id))->toBeNull();
});

it('can search workout templates by name', function () {
    $matchingTemplate = WorkoutTemplate::factory()->create(['name' => 'Push Day']);
    $nonMatchingTemplate = WorkoutTemplate::factory()->create(['name' => 'Pull Day']);

    livewire(ListWorkoutTemplates::class)
        ->searchTable('Push')
        ->assertCanSeeTableRecords([$matchingTemplate])
        ->assertCanNotSeeTableRecords([$nonMatchingTemplate]);
});

it('can sort workout templates by name', function () {
    WorkoutTemplate::factory()->create(['name' => 'Alpha']);
    WorkoutTemplate::factory()->create(['name' => 'Beta']);
    WorkoutTemplate::factory()->create(['name' => 'Gamma']);

    livewire(ListWorkoutTemplates::class)
        ->sortTable('name')
        ->assertCanSeeTableRecords(WorkoutTemplate::query()->orderBy('name')->get(), inOrder: true)
        ->sortTable('name', 'desc')
        ->assertCanSeeTableRecords(WorkoutTemplate::query()->orderBy('name', 'desc')->get(), inOrder: true);
});

it('can bulk delete workout templates', function () {
    $templates = WorkoutTemplate::factory()->count(3)->create();

    livewire(ListWorkoutTemplates::class)
        ->assertCanSeeTableRecords($templates)
        ->selectTableRecords($templates)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertCanNotSeeTableRecords($templates);

    $templates->each(fn (WorkoutTemplate $template) => expect(WorkoutTemplate::find($template->id))->toBeNull());
});

it('displays exercise count in the table', function () {
    $template = WorkoutTemplate::factory()->create();
    WorkoutTemplateExercise::factory()->count(3)->create(['workout_template_id' => $template->id]);

    livewire(ListWorkoutTemplates::class)
        ->assertCanSeeTableRecords([$template]);
});

it('has correct navigation icon', function () {
    expect(WorkoutTemplateResource::getNavigationIcon())->not->toBeNull();
});
