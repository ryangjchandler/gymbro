<?php

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use App\Filament\Resources\Exercises\ExerciseResource;
use App\Filament\Resources\Exercises\Pages\CreateExercise;
use App\Filament\Resources\Exercises\Pages\EditExercise;
use App\Filament\Resources\Exercises\Pages\ListExercises;
use App\Filament\Resources\Exercises\Pages\ViewExercise;
use App\Models\Exercise;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can render the list page', function () {
    $exercises = Exercise::factory()->count(5)->create();

    livewire(ListExercises::class)
        ->assertOk()
        ->assertCanSeeTableRecords($exercises);
});

it('can render the create page', function () {
    livewire(CreateExercise::class)
        ->assertOk();
});

it('can create an exercise', function () {
    livewire(CreateExercise::class)
        ->fillForm([
            'name' => 'Bench Press',
            'muscle_group' => MuscleGroup::Chest,
            'type' => ExerciseType::Strength,
            'instructions' => 'Lie on bench, grip bar, lower to chest, press up.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Exercise::where('name', 'Bench Press')->first())
        ->not->toBeNull()
        ->muscle_group->toBe(MuscleGroup::Chest)
        ->type->toBe(ExerciseType::Strength);
});

it('validates required fields when creating', function () {
    livewire(CreateExercise::class)
        ->fillForm([
            'name' => '',
            'muscle_group' => null,
            'type' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'muscle_group' => 'required',
            'type' => 'required',
        ]);
});

it('can render the view page', function () {
    $exercise = Exercise::factory()->create();

    livewire(ViewExercise::class, ['record' => $exercise->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => $exercise->name,
            'muscle_group' => $exercise->muscle_group,
            'type' => $exercise->type,
        ]);
});

it('can render the edit page', function () {
    $exercise = Exercise::factory()->create();

    livewire(EditExercise::class, ['record' => $exercise->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => $exercise->name,
            'muscle_group' => $exercise->muscle_group,
            'type' => $exercise->type,
        ]);
});

it('can update an exercise', function () {
    $exercise = Exercise::factory()->create();

    livewire(EditExercise::class, ['record' => $exercise->getRouteKey()])
        ->fillForm([
            'name' => 'Updated Exercise Name',
            'muscle_group' => MuscleGroup::Back,
            'type' => ExerciseType::Cardio,
            'instructions' => 'Updated instructions.',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($exercise->fresh())
        ->name->toBe('Updated Exercise Name')
        ->muscle_group->toBe(MuscleGroup::Back)
        ->type->toBe(ExerciseType::Cardio);
});

it('can delete an exercise from the edit page', function () {
    $exercise = Exercise::factory()->create();

    livewire(EditExercise::class, ['record' => $exercise->getRouteKey()])
        ->callAction(DeleteAction::class);

    expect(Exercise::find($exercise->id))->toBeNull();
});

it('can search exercises by name', function () {
    $matchingExercise = Exercise::factory()->create(['name' => 'Bench Press']);
    $nonMatchingExercise = Exercise::factory()->create(['name' => 'Deadlift']);

    livewire(ListExercises::class)
        ->searchTable('Bench')
        ->assertCanSeeTableRecords([$matchingExercise])
        ->assertCanNotSeeTableRecords([$nonMatchingExercise]);
});

it('can sort exercises by name', function () {
    Exercise::factory()->create(['name' => 'Alpha']);
    Exercise::factory()->create(['name' => 'Beta']);
    Exercise::factory()->create(['name' => 'Gamma']);

    livewire(ListExercises::class)
        ->sortTable('name')
        ->assertCanSeeTableRecords(Exercise::query()->orderBy('name')->get(), inOrder: true)
        ->sortTable('name', 'desc')
        ->assertCanSeeTableRecords(Exercise::query()->orderBy('name', 'desc')->get(), inOrder: true);
});

it('can filter exercises by muscle group', function () {
    $chestExercise = Exercise::factory()->create(['muscle_group' => MuscleGroup::Chest]);
    $backExercise = Exercise::factory()->create(['muscle_group' => MuscleGroup::Back]);

    livewire(ListExercises::class)
        ->assertCanSeeTableRecords([$chestExercise, $backExercise])
        ->filterTable('muscle_group', MuscleGroup::Chest->value)
        ->assertCanSeeTableRecords([$chestExercise])
        ->assertCanNotSeeTableRecords([$backExercise]);
});

it('can filter exercises by type', function () {
    $strengthExercise = Exercise::factory()->strength()->create();
    $cardioExercise = Exercise::factory()->cardio()->create();

    livewire(ListExercises::class)
        ->assertCanSeeTableRecords([$strengthExercise, $cardioExercise])
        ->filterTable('type', ExerciseType::Strength->value)
        ->assertCanSeeTableRecords([$strengthExercise])
        ->assertCanNotSeeTableRecords([$cardioExercise]);
});

it('can bulk delete exercises', function () {
    $exercises = Exercise::factory()->count(3)->create();

    livewire(ListExercises::class)
        ->assertCanSeeTableRecords($exercises)
        ->selectTableRecords($exercises)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertCanNotSeeTableRecords($exercises);

    $exercises->each(fn (Exercise $exercise) => expect(Exercise::find($exercise->id))->toBeNull());
});

it('has correct navigation icon', function () {
    expect(ExerciseResource::getNavigationIcon())->not->toBeNull();
});
