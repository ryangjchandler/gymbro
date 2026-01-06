<?php

use App\Enums\Achievement;
use App\Filament\Pages\Achievements;
use App\Models\User;
use App\Models\UserAchievement;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the achievements page', function () {
    livewire(Achievements::class)
        ->assertOk()
        ->assertSee('Achievements');
});

it('displays all achievement categories', function () {
    livewire(Achievements::class)
        ->assertOk()
        ->assertSee('Consistency')
        ->assertSee('Strength')
        ->assertSee('Volume')
        ->assertSee('Variety')
        ->assertSee('Body Weight');
});

it('shows unlocked achievement count', function () {
    UserAchievement::factory()->achievement(Achievement::FirstWorkout)->create();
    UserAchievement::factory()->achievement(Achievement::FirstPR)->create();

    livewire(Achievements::class)
        ->assertOk()
        ->assertSee('2 of 26 achievements unlocked');
});

it('displays unlocked achievements with achieved date', function () {
    UserAchievement::factory()
        ->achievement(Achievement::FirstWorkout)
        ->achievedAt(now()->subDay())
        ->create();

    livewire(Achievements::class)
        ->assertOk()
        ->assertSee('First Steps')
        ->assertSee(now()->subDay()->format('M j, Y'));
});

it('displays locked achievements with progress', function () {
    livewire(Achievements::class)
        ->assertOk()
        ->assertSee('0 / 1'); // FirstWorkout threshold
});

it('shows achievement descriptions', function () {
    livewire(Achievements::class)
        ->assertOk()
        ->assertSee('Complete your first workout');
});

it('shows achievement tiers', function () {
    livewire(Achievements::class)
        ->assertOk()
        ->assertSee('Bronze')
        ->assertSee('Silver')
        ->assertSee('Gold');
});

it('returns correct unlocked count', function () {
    UserAchievement::factory()->achievement(Achievement::FirstWorkout)->create();
    UserAchievement::factory()->achievement(Achievement::FirstPR)->create();
    UserAchievement::factory()->achievement(Achievement::FirstWeighIn)->create();

    $page = new Achievements;

    expect($page->getUnlockedCount())->toBe(3);
});

it('returns correct total count', function () {
    $page = new Achievements;

    expect($page->getTotalCount())->toBe(26);
});
