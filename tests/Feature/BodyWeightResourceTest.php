<?php

use App\Filament\Resources\BodyWeights\BodyWeightResource;
use App\Filament\Resources\BodyWeights\Pages\CreateBodyWeight;
use App\Filament\Resources\BodyWeights\Pages\EditBodyWeight;
use App\Filament\Resources\BodyWeights\Pages\ListBodyWeights;
use App\Filament\Resources\BodyWeights\Pages\ViewBodyWeight;
use App\Models\BodyWeight;
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
    $bodyWeights = BodyWeight::factory()->count(3)->create();

    livewire(ListBodyWeights::class)
        ->assertOk()
        ->assertCanSeeTableRecords($bodyWeights);
});

it('can render the create page', function () {
    livewire(CreateBodyWeight::class)
        ->assertOk();
});

it('can create a body weight entry', function () {
    livewire(CreateBodyWeight::class)
        ->fillForm([
            'recorded_at' => '2026-01-15',
            'stones' => 12,
            'pounds' => 5.5,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(BodyWeight::whereDate('recorded_at', '2026-01-15')
        ->where('stones', 12)
        ->where('pounds', 5.5)
        ->exists())->toBeTrue();
});

it('can create a body weight entry with notes', function () {
    livewire(CreateBodyWeight::class)
        ->fillForm([
            'recorded_at' => '2026-01-15',
            'stones' => 12,
            'pounds' => 7.0,
            'notes' => 'Feeling good!',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(BodyWeight::where('notes', 'Feeling good!')->exists())->toBeTrue();
});

it('validates required fields when creating', function () {
    livewire(CreateBodyWeight::class)
        ->fillForm([
            'recorded_at' => null,
            'stones' => null,
            'pounds' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'recorded_at' => 'required',
            'stones' => 'required',
            'pounds' => 'required',
        ]);
});

it('can render the view page', function () {
    $bodyWeight = BodyWeight::factory()->create();

    livewire(ViewBodyWeight::class, ['record' => $bodyWeight->getRouteKey()])
        ->assertOk();
});

it('can render the edit page', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'stones' => 11,
        'pounds' => 8.5,
    ]);

    livewire(EditBodyWeight::class, ['record' => $bodyWeight->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet([
            'stones' => 11,
            'pounds' => 8.5,
        ]);
});

it('can update a body weight entry', function () {
    $bodyWeight = BodyWeight::factory()->create();

    livewire(EditBodyWeight::class, ['record' => $bodyWeight->getRouteKey()])
        ->fillForm([
            'recorded_at' => '2026-02-01',
            'stones' => 13,
            'pounds' => 2.0,
            'notes' => 'Updated notes',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(BodyWeight::where('id', $bodyWeight->id)
        ->whereDate('recorded_at', '2026-02-01')
        ->where('stones', 13)
        ->where('pounds', 2.0)
        ->where('notes', 'Updated notes')
        ->exists())->toBeTrue();
});

it('can delete a body weight entry from the edit page', function () {
    $bodyWeight = BodyWeight::factory()->create();

    livewire(EditBodyWeight::class, ['record' => $bodyWeight->getRouteKey()])
        ->callAction(DeleteAction::class);

    expect(BodyWeight::find($bodyWeight->id))->toBeNull();
});

it('can sort body weights by recorded date', function () {
    BodyWeight::factory()->create(['recorded_at' => '2026-01-10']);
    BodyWeight::factory()->create(['recorded_at' => '2026-01-05']);
    BodyWeight::factory()->create(['recorded_at' => '2026-01-15']);

    livewire(ListBodyWeights::class)
        ->sortTable('recorded_at')
        ->assertCanSeeTableRecords(BodyWeight::query()->orderBy('recorded_at')->get(), inOrder: true)
        ->sortTable('recorded_at', 'desc')
        ->assertCanSeeTableRecords(BodyWeight::query()->orderBy('recorded_at', 'desc')->get(), inOrder: true);
});

it('defaults to sorting by recorded date descending', function () {
    BodyWeight::factory()->create(['recorded_at' => '2026-01-10']);
    BodyWeight::factory()->create(['recorded_at' => '2026-01-05']);
    BodyWeight::factory()->create(['recorded_at' => '2026-01-15']);

    livewire(ListBodyWeights::class)
        ->assertCanSeeTableRecords(BodyWeight::query()->orderBy('recorded_at', 'desc')->get(), inOrder: true);
});

it('can bulk delete body weight entries', function () {
    $bodyWeights = BodyWeight::factory()->count(3)->create();

    livewire(ListBodyWeights::class)
        ->assertCanSeeTableRecords($bodyWeights)
        ->selectTableRecords($bodyWeights)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertCanNotSeeTableRecords($bodyWeights);

    $bodyWeights->each(fn (BodyWeight $bodyWeight) => expect(BodyWeight::find($bodyWeight->id))->toBeNull());
});

it('has correct navigation icon', function () {
    expect(BodyWeightResource::getNavigationIcon())->not->toBeNull();
});

it('displays formatted weight correctly', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 6.5,
    ]);

    expect($bodyWeight->formatted_weight)->toBe('12st 6.5lbs');
});

it('calculates total kilograms correctly', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'stones' => 10,
        'pounds' => 0,
    ]);

    // 10 stones = 140 lbs = 63.5 kg
    expect($bodyWeight->total_kilograms)->toBe(63.5);
});
