<?php

use App\Filament\Resources\Exercises\Pages\ListExercises;
use App\Filament\Widgets\Estimated1rmWidget;
use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutSet;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

describe('exercise pinning', function () {
    it('can pin an unpinned exercise', function () {
        $exercise = Exercise::factory()->strength()->create(['is_pinned' => false]);

        livewire(ListExercises::class)
            ->callTableAction('togglePin', $exercise);

        expect($exercise->fresh()->is_pinned)->toBeTrue();
    });

    it('can unpin a pinned exercise', function () {
        $exercise = Exercise::factory()->strength()->pinned()->create();

        livewire(ListExercises::class)
            ->callTableAction('togglePin', $exercise);

        expect($exercise->fresh()->is_pinned)->toBeFalse();
    });

    it('prevents pinning more than 10 exercises', function () {
        Exercise::factory()->count(10)->strength()->pinned()->create();
        $unpinnedExercise = Exercise::factory()->strength()->create(['is_pinned' => false]);

        livewire(ListExercises::class)
            ->callTableAction('togglePin', $unpinnedExercise)
            ->assertNotified('Maximum pinned exercises reached');

        expect($unpinnedExercise->fresh()->is_pinned)->toBeFalse();
    });

    it('allows unpinning when at max pinned exercises', function () {
        $pinnedExercises = Exercise::factory()->count(10)->strength()->pinned()->create();
        $exerciseToUnpin = $pinnedExercises->first();

        livewire(ListExercises::class)
            ->callTableAction('togglePin', $exerciseToUnpin);

        expect($exerciseToUnpin->fresh()->is_pinned)->toBeFalse();
    });

    it('shows pinned status in table', function () {
        $pinnedExercise = Exercise::factory()->strength()->pinned()->create();
        $unpinnedExercise = Exercise::factory()->strength()->create(['is_pinned' => false]);

        livewire(ListExercises::class)
            ->assertCanSeeTableRecords([$pinnedExercise, $unpinnedExercise])
            ->assertTableColumnStateSet('is_pinned', true, $pinnedExercise)
            ->assertTableColumnStateSet('is_pinned', false, $unpinnedExercise);
    });
});

describe('Estimated1rmWidget', function () {
    it('renders successfully', function () {
        livewire(Estimated1rmWidget::class)
            ->assertOk();
    });

    it('shows empty state when no exercises are pinned', function () {
        Exercise::factory()->count(3)->strength()->create(['is_pinned' => false]);

        livewire(Estimated1rmWidget::class)
            ->assertSee('No pinned exercises');
    });

    it('shows pinned strength exercises', function () {
        $pinnedExercise = Exercise::factory()->strength()->pinned()->create(['name' => 'Bench Press']);
        Exercise::factory()->strength()->create(['name' => 'Unpinned Exercise', 'is_pinned' => false]);

        livewire(Estimated1rmWidget::class)
            ->assertSee('Bench Press')
            ->assertDontSee('Unpinned Exercise');
    });

    it('does not show pinned cardio exercises', function () {
        $cardioExercise = Exercise::factory()->cardio()->pinned()->create(['name' => 'Running']);
        $strengthExercise = Exercise::factory()->strength()->pinned()->create(['name' => 'Squat']);

        livewire(Estimated1rmWidget::class)
            ->assertSee('Squat')
            ->assertDontSee('Running');
    });

    it('shows estimated 1RM for pinned exercises with workout data', function () {
        $exercise = Exercise::factory()->strength()->pinned()->create(['name' => 'Deadlift']);

        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(100)
            ->withReps(8)
            ->create();

        livewire(Estimated1rmWidget::class)
            ->assertSee('Deadlift')
            ->assertSee('100.0kg × 8')
            ->assertSee('126.7kg');
    });

    it('shows dash when pinned exercise has no qualifying sets', function () {
        $exercise = Exercise::factory()->strength()->pinned()->create(['name' => 'Overhead Press']);

        livewire(Estimated1rmWidget::class)
            ->assertSee('Overhead Press')
            ->assertSeeHtml('-');
    });

    it('orders exercises alphabetically', function () {
        Exercise::factory()->strength()->pinned()->create(['name' => 'Zzz Last']);
        Exercise::factory()->strength()->pinned()->create(['name' => 'Aaa First']);
        Exercise::factory()->strength()->pinned()->create(['name' => 'Mmm Middle']);

        livewire(Estimated1rmWidget::class)
            ->assertSeeInOrder(['Aaa First', 'Mmm Middle', 'Zzz Last']);
    });
});
