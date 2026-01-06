<?php

use App\Enums\Achievement;
use App\Filament\Widgets\RecentAchievementsWidget;
use App\Models\User;
use App\Models\UserAchievement;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the recent achievements widget', function () {
    livewire(RecentAchievementsWidget::class)
        ->assertOk()
        ->assertSee('Recent Achievements');
});

it('displays recent achievements', function () {
    UserAchievement::factory()
        ->achievement(Achievement::FirstWorkout)
        ->achievedAt(now())
        ->create();

    livewire(RecentAchievementsWidget::class)
        ->assertOk()
        ->assertSee('First Steps')
        ->assertSee('Complete your first workout');
});

it('shows empty state when no achievements exist', function () {
    livewire(RecentAchievementsWidget::class)
        ->assertOk()
        ->assertSee('No achievements yet');
});

it('limits results to 5 records', function () {
    UserAchievement::factory()
        ->count(7)
        ->sequence(
            ['achievement' => Achievement::FirstWorkout],
            ['achievement' => Achievement::FirstPR],
            ['achievement' => Achievement::FirstWeighIn],
            ['achievement' => Achievement::Streak3Days],
            ['achievement' => Achievement::Volume10k],
            ['achievement' => Achievement::Exercises5],
            ['achievement' => Achievement::Workouts10],
        )
        ->achievedAt(now())
        ->create();

    livewire(RecentAchievementsWidget::class)
        ->assertOk();
});

it('orders records by achieved_at descending', function () {
    $older = UserAchievement::factory()
        ->achievement(Achievement::FirstWorkout)
        ->achievedAt(now()->subDays(5))
        ->create();

    $newer = UserAchievement::factory()
        ->achievement(Achievement::FirstPR)
        ->achievedAt(now())
        ->create();

    livewire(RecentAchievementsWidget::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$newer, $older]);
});

it('displays achievement tier badges', function () {
    UserAchievement::factory()
        ->achievement(Achievement::FirstWorkout)
        ->achievedAt(now())
        ->create();

    livewire(RecentAchievementsWidget::class)
        ->assertOk()
        ->assertSee('Bronze');
});

it('has responsive column span', function () {
    $widget = new RecentAchievementsWidget;
    expect($widget->getColumnSpan())->toBe([
        'default' => 'full',
        'lg' => 2,
    ]);
});

it('has correct sort order', function () {
    expect(RecentAchievementsWidget::getSort())->toBe(5);
});

it('is not paginated', function () {
    UserAchievement::factory()
        ->count(10)
        ->sequence(
            ['achievement' => Achievement::FirstWorkout],
            ['achievement' => Achievement::FirstPR],
            ['achievement' => Achievement::FirstWeighIn],
            ['achievement' => Achievement::Streak3Days],
            ['achievement' => Achievement::Volume10k],
            ['achievement' => Achievement::Exercises5],
            ['achievement' => Achievement::Workouts10],
            ['achievement' => Achievement::PRs10],
            ['achievement' => Achievement::Exercises20],
            ['achievement' => Achievement::Workouts50],
        )
        ->achievedAt(now())
        ->create();

    livewire(RecentAchievementsWidget::class)
        ->assertOk();
});
