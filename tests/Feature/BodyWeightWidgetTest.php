<?php

use App\Filament\Widgets\BodyWeightWidget;
use App\Models\BodyWeight;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the body weight widget', function () {
    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('Current Weight');
});

it('shows no data message when no body weight records exist', function () {
    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('No data')
        ->assertSee('Log your first weigh-in');
});

it('displays current weight when records exist', function () {
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 7,
        'recorded_at' => now(),
    ]);

    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('12st 7lbs');
});

it('displays the most recent weight', function () {
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now()->subDays(5),
    ]);

    BodyWeight::factory()->create([
        'stones' => 11,
        'pounds' => 10,
        'recorded_at' => now(),
    ]);

    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('11st 10lbs');
});

it('shows 30 day change stat', function () {
    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('30 Day Change');
});

it('shows not enough data when no records from 30 days ago', function () {
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now(),
    ]);

    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('Not enough data')
        ->assertSee('Need data from 30+ days ago');
});

it('calculates weight loss over 30 days', function () {
    // 35 days ago: 13st 0lbs (182 lbs)
    BodyWeight::factory()->create([
        'stones' => 13,
        'pounds' => 0,
        'recorded_at' => now()->subDays(35),
    ]);

    // Today: 12st 0lbs (168 lbs) - 14 lbs loss = 1 stone
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now(),
    ]);

    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('Weight loss');
});

it('calculates weight gain over 30 days', function () {
    // 35 days ago: 11st 0lbs (154 lbs)
    BodyWeight::factory()->create([
        'stones' => 11,
        'pounds' => 0,
        'recorded_at' => now()->subDays(35),
    ]);

    // Today: 12st 0lbs (168 lbs) - 14 lbs gain = 1 stone
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now(),
    ]);

    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('Weight gain');
});

it('shows no change when weight is the same', function () {
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now()->subDays(35),
    ]);

    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now(),
    ]);

    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('No change')
        ->assertSee('Maintaining weight');
});

it('shows how long ago the last weigh-in was', function () {
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now()->subDays(2),
    ]);

    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('2 days ago');
});

it('has correct sort order', function () {
    expect(BodyWeightWidget::getSort())->toBe(3);
});

it('generates chart data from body weight records', function () {
    // Create some weight records
    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 0,
        'recorded_at' => now()->subDays(3),
    ]);

    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 3,
        'recorded_at' => now()->subDays(2),
    ]);

    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 1,
        'recorded_at' => now()->subDay(),
    ]);

    livewire(BodyWeightWidget::class)
        ->assertOk();
});

it('handles 30 day change with exact boundary date', function () {
    // Exactly 30 days ago
    BodyWeight::factory()->create([
        'stones' => 13,
        'pounds' => 0,
        'recorded_at' => now()->subDays(30),
    ]);

    BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 7,
        'recorded_at' => now(),
    ]);

    livewire(BodyWeightWidget::class)
        ->assertOk()
        ->assertSee('Weight loss');
});
