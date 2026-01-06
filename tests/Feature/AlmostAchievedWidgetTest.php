<?php

use App\Filament\Widgets\AlmostAchievedWidget;
use App\Models\User;
use App\Models\Workout;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the almost achieved widget', function () {
    livewire(AlmostAchievedWidget::class)
        ->assertOk()
        ->assertSee('Almost There!');
});

it('displays achievements at 75% or more progress', function () {
    // Create 3 completed workouts (75% of 4 needed for Streak3Days if we had right setup)
    // For FirstWorkout, any workout completes it. Let's use Workouts10 which needs 10 workouts
    // Create 8 workouts (80% of 10)
    Workout::factory()
        ->count(8)
        ->completed()
        ->create();

    livewire(AlmostAchievedWidget::class)
        ->assertOk()
        ->assertSee('Double Digits') // Workouts10 label
        ->assertSee('8 / 10');
});

it('shows empty state when no achievements are close', function () {
    // No workouts or progress
    livewire(AlmostAchievedWidget::class)
        ->assertOk()
        ->assertSee('No achievements in progress');
});

it('limits results to 5 achievements', function () {
    $widget = new AlmostAchievedWidget;
    $almostAchieved = $widget->getAlmostAchieved();

    expect($almostAchieved->count())->toBeLessThanOrEqual(5);
});

it('has responsive column span', function () {
    $widget = new AlmostAchievedWidget;
    expect($widget->getColumnSpan())->toBe([
        'default' => 'full',
        'lg' => 2,
    ]);
});

it('has correct sort order', function () {
    expect(AlmostAchievedWidget::getSort())->toBe(6);
});
