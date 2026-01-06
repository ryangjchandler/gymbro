<?php

use App\Filament\Widgets\BodyWeightChart;
use App\Models\BodyWeight;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the body weight chart widget', function () {
    livewire(BodyWeightChart::class)
        ->assertOk()
        ->assertSee('Body Weight Over Time');
});

it('has correct sort order', function () {
    expect(BodyWeightChart::getSort())->toBe(6);
});

it('has responsive column span', function () {
    $widget = new BodyWeightChart;
    expect($widget->getColumnSpan())->toBe([
        'default' => 'full',
        'lg' => 2,
    ]);
});

it('shows empty data when no body weights exist', function () {
    $widget = new BodyWeightChart;
    $widget->filters = ['period' => '90'];

    $data = invade($widget)->getData();

    expect($data['datasets'])->toBeEmpty()
        ->and($data['labels'])->toBeEmpty();
});

it('shows body weight data when records exist', function () {
    BodyWeight::factory()->count(5)->create([
        'recorded_at' => now()->subDays(rand(1, 30)),
    ]);

    $widget = new BodyWeightChart;
    $widget->filters = ['period' => '90'];

    $data = invade($widget)->getData();

    expect($data['datasets'])->not->toBeEmpty()
        ->and($data['labels'])->not->toBeEmpty();
});

it('orders data chronologically', function () {
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now()->subDays(10),
    ]);
    BodyWeight::factory()->create([
        'stones' => 11,
        'pounds' => 10,
        'recorded_at' => now()->subDays(5),
    ]);
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 5,
        'recorded_at' => now(),
    ]);

    $widget = new BodyWeightChart;
    $widget->filters = ['period' => '90'];

    $data = invade($widget)->getData();

    // 12st 0lbs = 168 lbs, 11st 10lbs = 164 lbs, 12st 5lbs = 173 lbs
    expect($data['datasets'][0]['data'][0])->toBe(168.0)
        ->and($data['datasets'][0]['data'][1])->toBe(164.0)
        ->and($data['datasets'][0]['data'][2])->toBe(173.0);
});

it('filters by 30 day period', function () {
    // Old record (outside 30 days)
    BodyWeight::factory()->create([
        'stones' => 13,
        'pounds' => 0,
        'recorded_at' => now()->subDays(60),
    ]);

    // Recent record (within 30 days)
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now()->subDays(10),
    ]);

    $widget = new BodyWeightChart;
    $widget->filters = ['period' => '30'];

    $data = invade($widget)->getData();

    expect($data['datasets'][0]['data'])->toHaveCount(1);
});

it('shows all data with all time filter', function () {
    BodyWeight::factory()->create([
        'recorded_at' => now()->subDays(400),
    ]);
    BodyWeight::factory()->create([
        'recorded_at' => now()->subDays(200),
    ]);
    BodyWeight::factory()->create([
        'recorded_at' => now(),
    ]);

    $widget = new BodyWeightChart;
    $widget->filters = ['period' => 'all'];

    $data = invade($widget)->getData();

    expect($data['datasets'][0]['data'])->toHaveCount(3);
});

it('converts weight to total pounds for chart', function () {
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 7,
        'recorded_at' => now(),
    ]);

    $widget = new BodyWeightChart;
    $widget->filters = ['period' => '90'];

    $data = invade($widget)->getData();

    // 12st 7lbs = (12 * 14) + 7 = 175 lbs
    expect($data['datasets'][0]['data'][0])->toBe(175.0);
});

it('is a line chart', function () {
    $widget = new BodyWeightChart;
    expect(invade($widget)->getType())->toBe('line');
});

it('formats labels as month and day', function () {
    BodyWeight::factory()->create([
        'recorded_at' => now()->setMonth(6)->setDay(15),
    ]);

    $widget = new BodyWeightChart;
    $widget->filters = ['period' => 'all'];

    $data = invade($widget)->getData();

    expect($data['labels'][0])->toBe('Jun 15');
});
