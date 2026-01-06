<?php

use App\Enums\PersonalRecordType;
use App\Filament\Resources\PersonalRecords\Pages\CreatePersonalRecord;
use App\Filament\Resources\PersonalRecords\Pages\EditPersonalRecord;
use App\Filament\Resources\PersonalRecords\Pages\ListPersonalRecords;
use App\Filament\Resources\PersonalRecords\Pages\ViewPersonalRecord;
use App\Filament\Resources\PersonalRecords\PersonalRecordResource;
use App\Models\Exercise;
use App\Models\PersonalRecord;
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
    $personalRecords = PersonalRecord::factory()->count(3)->create();

    livewire(ListPersonalRecords::class)
        ->assertOk()
        ->assertCanSeeTableRecords($personalRecords);
});

it('can render the create page', function () {
    livewire(CreatePersonalRecord::class)
        ->assertOk();
});

it('can create a personal record with max weight type', function () {
    $exercise = Exercise::factory()->create();

    livewire(CreatePersonalRecord::class)
        ->fillForm([
            'exercise_id' => $exercise->id,
            'type' => PersonalRecordType::MaxWeight,
            'value' => 120.5,
            'achieved_at' => '2026-01-15',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(PersonalRecord::where('exercise_id', $exercise->id)
        ->where('type', PersonalRecordType::MaxWeight)
        ->where('value', 120.5)
        ->whereDate('achieved_at', '2026-01-15')
        ->exists())->toBeTrue();
});

it('can create a personal record with max reps type', function () {
    $exercise = Exercise::factory()->create();

    livewire(CreatePersonalRecord::class)
        ->fillForm([
            'exercise_id' => $exercise->id,
            'type' => PersonalRecordType::MaxReps,
            'value' => 25,
            'achieved_at' => '2026-01-15',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(PersonalRecord::where('exercise_id', $exercise->id)
        ->where('type', PersonalRecordType::MaxReps)
        ->where('value', 25)
        ->exists())->toBeTrue();
});

it('can create a personal record with max volume type', function () {
    $exercise = Exercise::factory()->create();

    livewire(CreatePersonalRecord::class)
        ->fillForm([
            'exercise_id' => $exercise->id,
            'type' => PersonalRecordType::MaxVolume,
            'value' => 1500,
            'achieved_at' => '2026-01-15',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(PersonalRecord::where('exercise_id', $exercise->id)
        ->where('type', PersonalRecordType::MaxVolume)
        ->where('value', 1500)
        ->exists())->toBeTrue();
});

it('validates required fields when creating', function () {
    livewire(CreatePersonalRecord::class)
        ->fillForm([
            'exercise_id' => null,
            'type' => null,
            'value' => null,
            'achieved_at' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'exercise_id' => 'required',
            'type' => 'required',
            'value' => 'required',
            'achieved_at' => 'required',
        ]);
});

it('can render the view page', function () {
    $personalRecord = PersonalRecord::factory()->create();

    livewire(ViewPersonalRecord::class, ['record' => $personalRecord->getRouteKey()])
        ->assertOk();
});

it('can render the edit page', function () {
    $exercise = Exercise::factory()->create();
    $personalRecord = PersonalRecord::factory()->create([
        'exercise_id' => $exercise->id,
        'type' => PersonalRecordType::MaxWeight,
        'value' => 100,
    ]);

    livewire(EditPersonalRecord::class, ['record' => $personalRecord->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet([
            'exercise_id' => $exercise->id,
            'type' => PersonalRecordType::MaxWeight,
            'value' => 100,
        ]);
});

it('can update a personal record', function () {
    $personalRecord = PersonalRecord::factory()->create();
    $newExercise = Exercise::factory()->create();

    livewire(EditPersonalRecord::class, ['record' => $personalRecord->getRouteKey()])
        ->fillForm([
            'exercise_id' => $newExercise->id,
            'type' => PersonalRecordType::MaxReps,
            'value' => 30,
            'achieved_at' => '2026-02-01',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(PersonalRecord::where('id', $personalRecord->id)
        ->where('exercise_id', $newExercise->id)
        ->where('type', PersonalRecordType::MaxReps)
        ->where('value', 30)
        ->whereDate('achieved_at', '2026-02-01')
        ->exists())->toBeTrue();
});

it('can delete a personal record from the edit page', function () {
    $personalRecord = PersonalRecord::factory()->create();

    livewire(EditPersonalRecord::class, ['record' => $personalRecord->getRouteKey()])
        ->callAction(DeleteAction::class);

    expect(PersonalRecord::find($personalRecord->id))->toBeNull();
});

it('can sort personal records by achieved date', function () {
    PersonalRecord::factory()->create(['achieved_at' => '2026-01-10']);
    PersonalRecord::factory()->create(['achieved_at' => '2026-01-05']);
    PersonalRecord::factory()->create(['achieved_at' => '2026-01-15']);

    livewire(ListPersonalRecords::class)
        ->sortTable('achieved_at')
        ->assertCanSeeTableRecords(PersonalRecord::query()->orderBy('achieved_at')->get(), inOrder: true)
        ->sortTable('achieved_at', 'desc')
        ->assertCanSeeTableRecords(PersonalRecord::query()->orderBy('achieved_at', 'desc')->get(), inOrder: true);
});

it('defaults to sorting by achieved date descending', function () {
    PersonalRecord::factory()->create(['achieved_at' => '2026-01-10']);
    PersonalRecord::factory()->create(['achieved_at' => '2026-01-05']);
    PersonalRecord::factory()->create(['achieved_at' => '2026-01-15']);

    livewire(ListPersonalRecords::class)
        ->assertCanSeeTableRecords(PersonalRecord::query()->orderBy('achieved_at', 'desc')->get(), inOrder: true);
});

it('can filter personal records by type', function () {
    $maxWeightRecord = PersonalRecord::factory()->maxWeight()->create();
    $maxRepsRecord = PersonalRecord::factory()->maxReps()->create();
    $maxVolumeRecord = PersonalRecord::factory()->maxVolume()->create();

    livewire(ListPersonalRecords::class)
        ->assertCanSeeTableRecords([$maxWeightRecord, $maxRepsRecord, $maxVolumeRecord])
        ->filterTable('type', PersonalRecordType::MaxWeight->value)
        ->assertCanSeeTableRecords([$maxWeightRecord])
        ->assertCanNotSeeTableRecords([$maxRepsRecord, $maxVolumeRecord]);
});

it('can filter personal records by exercise', function () {
    $exercise1 = Exercise::factory()->create();
    $exercise2 = Exercise::factory()->create();

    $record1 = PersonalRecord::factory()->create(['exercise_id' => $exercise1->id]);
    $record2 = PersonalRecord::factory()->create(['exercise_id' => $exercise2->id]);

    livewire(ListPersonalRecords::class)
        ->assertCanSeeTableRecords([$record1, $record2])
        ->filterTable('exercise_id', $exercise1->id)
        ->assertCanSeeTableRecords([$record1])
        ->assertCanNotSeeTableRecords([$record2]);
});

it('can search personal records by exercise name', function () {
    $benchPress = Exercise::factory()->create(['name' => 'Bench Press']);
    $squat = Exercise::factory()->create(['name' => 'Squat']);

    $benchRecord = PersonalRecord::factory()->create(['exercise_id' => $benchPress->id]);
    $squatRecord = PersonalRecord::factory()->create(['exercise_id' => $squat->id]);

    livewire(ListPersonalRecords::class)
        ->searchTable('Bench')
        ->assertCanSeeTableRecords([$benchRecord])
        ->assertCanNotSeeTableRecords([$squatRecord]);
});

it('can bulk delete personal records', function () {
    $personalRecords = PersonalRecord::factory()->count(3)->create();

    livewire(ListPersonalRecords::class)
        ->assertCanSeeTableRecords($personalRecords)
        ->selectTableRecords($personalRecords)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertCanNotSeeTableRecords($personalRecords);

    $personalRecords->each(fn (PersonalRecord $record) => expect(PersonalRecord::find($record->id))->toBeNull());
});

it('has correct navigation icon', function () {
    expect(PersonalRecordResource::getNavigationIcon())->not->toBeNull();
});

it('displays formatted value correctly for max weight', function () {
    $personalRecord = PersonalRecord::factory()->maxWeight(120.5)->create();

    expect($personalRecord->formattedValue)->toBe('120.5kg');
});

it('displays formatted value correctly for max reps', function () {
    $personalRecord = PersonalRecord::factory()->maxReps(25)->create();

    expect($personalRecord->formattedValue)->toBe('25 reps');
});

it('displays formatted value correctly for max volume', function () {
    $personalRecord = PersonalRecord::factory()->maxVolume(1500)->create();

    expect($personalRecord->formattedValue)->toBe('1,500kg');
});
