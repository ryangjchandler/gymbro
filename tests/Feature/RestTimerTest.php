<?php

use App\Livewire\RestTimer;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can render the rest timer component', function () {
    livewire(RestTimer::class)
        ->assertOk()
        ->assertSet('seconds', 45)
        ->assertSeeHtml('x-text="formattedTime"');
});

it('uses default rest seconds of 45', function () {
    livewire(RestTimer::class)
        ->assertSet('seconds', 45)
        ->assertSet('defaultSeconds', 45)
        ->assertSet('totalSeconds', 45)
        ->assertSet('isRunning', false);
});

it('can be initialized with custom seconds', function () {
    livewire(RestTimer::class, ['seconds' => 60])
        ->assertSet('seconds', 60)
        ->assertSet('defaultSeconds', 60)
        ->assertSet('totalSeconds', 60);
});

it('can start the timer', function () {
    livewire(RestTimer::class)
        ->call('start')
        ->assertSet('isRunning', true)
        ->assertDispatched('timer-started');
});

it('can start the timer with custom seconds', function () {
    livewire(RestTimer::class)
        ->call('start', 90)
        ->assertSet('seconds', 90)
        ->assertSet('totalSeconds', 90)
        ->assertSet('isRunning', true)
        ->assertDispatched('timer-started');
});

it('can stop the timer', function () {
    livewire(RestTimer::class)
        ->call('start')
        ->assertSet('isRunning', true)
        ->call('stop')
        ->assertSet('isRunning', false)
        ->assertDispatched('timer-stopped');
});

it('can reset the timer', function () {
    livewire(RestTimer::class)
        ->set('seconds', 30)
        ->call('start')
        ->call('resetTimer')
        ->assertSet('seconds', 45)
        ->assertSet('totalSeconds', 45)
        ->assertSet('isRunning', false)
        ->assertDispatched('timer-reset');
});

it('can complete the timer', function () {
    livewire(RestTimer::class)
        ->call('start')
        ->call('complete')
        ->assertSet('isRunning', false)
        ->assertDispatched('timer-completed');
});

it('can add time to the timer', function () {
    livewire(RestTimer::class)
        ->assertSet('seconds', 45)
        ->call('addTime', 15)
        ->assertSet('seconds', 60)
        ->assertDispatched('timer-adjusted');
});

it('can subtract time from the timer', function () {
    livewire(RestTimer::class)
        ->assertSet('seconds', 45)
        ->call('subtractTime', 15)
        ->assertSet('seconds', 30)
        ->assertDispatched('timer-adjusted');
});

it('does not allow negative seconds when subtracting', function () {
    livewire(RestTimer::class, ['seconds' => 10])
        ->call('subtractTime', 20)
        ->assertSet('seconds', 0);
});

it('resets to the custom default seconds', function () {
    livewire(RestTimer::class, ['seconds' => 90])
        ->set('seconds', 30)
        ->call('resetTimer')
        ->assertSet('seconds', 90);
});

it('displays quick preset buttons', function () {
    livewire(RestTimer::class)
        ->assertSee('30s')
        ->assertSee('45s')
        ->assertSee('60s')
        ->assertSee('90s')
        ->assertSee('120s');
});

it('can start timer from preset', function () {
    livewire(RestTimer::class)
        ->call('start', 120)
        ->assertSet('seconds', 120)
        ->assertSet('totalSeconds', 120)
        ->assertSet('isRunning', true);
});

it('sets totalSeconds to current seconds when starting', function () {
    livewire(RestTimer::class)
        ->set('seconds', 30)
        ->call('start')
        ->assertSet('seconds', 30)
        ->assertSet('totalSeconds', 30)
        ->assertSet('isRunning', true);
});

it('shows reset button text in view', function () {
    livewire(RestTimer::class)
        ->assertSee('Reset to 45s');
});

it('has the correct default constant', function () {
    expect(RestTimer::DEFAULT_REST_SECONDS)->toBe(45);
});
