<?php

use App\Filament\Widgets\WorkoutStatsWidget;
use App\Models\User;
use App\Models\Workout;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the workout stats widget', function () {
    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('This Week')
        ->assertSee('This Month')
        ->assertSee('Current Streak');
});

it('displays zero workouts this week when none exist', function () {
    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('This Week')
        ->assertSee('0');
});

it('counts workouts completed this week', function () {
    Workout::factory()->completed()->count(3)->create([
        'completed_at' => now(),
    ]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('3');
});

it('does not count pending workouts in this week', function () {
    Workout::factory()->pending()->count(2)->create();
    Workout::factory()->completed()->create([
        'completed_at' => now(),
    ]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('This Week');
});

it('shows increase compared to last week', function () {
    // Last week: 2 workouts
    Workout::factory()->completed()->count(2)->create([
        'completed_at' => now()->subWeek(),
    ]);

    // This week: 4 workouts
    Workout::factory()->completed()->count(4)->create([
        'completed_at' => now(),
    ]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('+2 vs last week');
});

it('shows decrease compared to last week', function () {
    // Last week: 5 workouts
    Workout::factory()->completed()->count(5)->create([
        'completed_at' => now()->subWeek(),
    ]);

    // This week: 2 workouts
    Workout::factory()->completed()->count(2)->create([
        'completed_at' => now(),
    ]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('-3 vs last week');
});

it('counts workouts completed this month', function () {
    Workout::factory()->completed()->count(10)->create([
        'completed_at' => now(),
    ]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('This Month')
        ->assertSee('10');
});

it('shows increase compared to last month', function () {
    // Last month: 8 workouts
    Workout::factory()->completed()->count(8)->create([
        'completed_at' => now()->subMonth(),
    ]);

    // This month: 12 workouts
    Workout::factory()->completed()->count(12)->create([
        'completed_at' => now(),
    ]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('+4 vs last month');
});

it('calculates streak as zero when no workouts', function () {
    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('0 days')
        ->assertSee('Start your streak today');
});

it('calculates streak of one day when workout completed today', function () {
    Workout::factory()->completed()->create([
        'completed_at' => now(),
    ]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('1 day');
});

it('calculates streak with consecutive days', function () {
    // 3 consecutive days including today
    Workout::factory()->completed()->create(['completed_at' => now()]);
    Workout::factory()->completed()->create(['completed_at' => now()->subDay()]);
    Workout::factory()->completed()->create(['completed_at' => now()->subDays(2)]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('3 days');
});

it('counts streak from yesterday if no workout today', function () {
    // Streak starting from yesterday
    Workout::factory()->completed()->create(['completed_at' => now()->subDay()]);
    Workout::factory()->completed()->create(['completed_at' => now()->subDays(2)]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('2 days');
});

it('breaks streak when gap in days', function () {
    // Today
    Workout::factory()->completed()->create(['completed_at' => now()]);
    // Gap: skip yesterday
    // 2 days ago
    Workout::factory()->completed()->create(['completed_at' => now()->subDays(2)]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('1 day');
});

it('shows streak is zero when most recent workout is more than a day old', function () {
    // Last workout was 3 days ago (not today or yesterday)
    Workout::factory()->completed()->create([
        'completed_at' => now()->subDays(3),
    ]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('0 days')
        ->assertSee('Start your streak today');
});

it('shows on fire message for 7 day streak', function () {
    // 7 consecutive days
    for ($i = 0; $i < 7; $i++) {
        Workout::factory()->completed()->create([
            'completed_at' => now()->subDays($i),
        ]);
    }

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('7 days')
        ->assertSee('On fire!');
});

it('shows keep it up message for 3-6 day streak', function () {
    // 4 consecutive days
    for ($i = 0; $i < 4; $i++) {
        Workout::factory()->completed()->create([
            'completed_at' => now()->subDays($i),
        ]);
    }

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('4 days')
        ->assertSee('Keep it up!');
});

it('counts multiple workouts on same day as one streak day', function () {
    // Two workouts today
    Workout::factory()->completed()->count(2)->create([
        'completed_at' => now(),
    ]);

    // One workout yesterday
    Workout::factory()->completed()->create([
        'completed_at' => now()->subDay(),
    ]);

    livewire(WorkoutStatsWidget::class)
        ->assertOk()
        ->assertSee('2 days');
});

it('has correct sort order', function () {
    expect(WorkoutStatsWidget::getSort())->toBe(2);
});
