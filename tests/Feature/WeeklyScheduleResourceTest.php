<?php

use App\Enums\WorkoutStatus;
use App\Filament\Resources\WeeklySchedules\Pages\ListWeeklySchedules;
use App\Filament\Resources\WeeklySchedules\WeeklyScheduleResource;
use App\Models\User;
use App\Models\WeeklySchedule;
use App\Models\Workout;
use App\Models\WorkoutTemplate;
use Carbon\Carbon;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can render the calendar page', function () {
    livewire(ListWeeklySchedules::class)
        ->assertOk();
});

it('displays all seven days of the week', function () {
    livewire(ListWeeklySchedules::class)
        ->assertSee('Monday')
        ->assertSee('Tuesday')
        ->assertSee('Wednesday')
        ->assertSee('Thursday')
        ->assertSee('Friday')
        ->assertSee('Saturday')
        ->assertSee('Sunday');
});

it('displays scheduled workout template names', function () {
    $template = WorkoutTemplate::factory()->create(['name' => 'Push Day']);
    WeeklySchedule::factory()->forDay(1)->create(['workout_template_id' => $template->id]);

    livewire(ListWeeklySchedules::class)
        ->assertSee('Push Day');
});

it('displays rest day for empty days', function () {
    livewire(ListWeeklySchedules::class)
        ->assertSee('Rest Day');
});

it('displays exercise count for scheduled days', function () {
    $template = WorkoutTemplate::factory()
        ->hasWorkoutTemplateExercises(3)
        ->create();
    WeeklySchedule::factory()->forDay(2)->create(['workout_template_id' => $template->id]);

    livewire(ListWeeklySchedules::class)
        ->assertSee('3 exercises');
});

it('displays paused badge for inactive schedules', function () {
    WeeklySchedule::factory()->forDay(3)->inactive()->create();

    livewire(ListWeeklySchedules::class)
        ->assertSee('Paused');
});

it('can open view modal for scheduled day', function () {
    $template = WorkoutTemplate::factory()->create(['name' => 'Leg Day']);
    WeeklySchedule::factory()->forDay(1)->create(['workout_template_id' => $template->id]);

    livewire(ListWeeklySchedules::class)
        ->call('selectDay', 1)
        ->assertSet('selectedDay', 1)
        ->assertActionMounted('viewSchedule');
});

it('can open create modal for empty day', function () {
    livewire(ListWeeklySchedules::class)
        ->call('selectEmptyDay', 4)
        ->assertSet('selectedDay', 4)
        ->assertActionMounted('createSchedule');
});

it('can create a weekly schedule via modal', function () {
    $template = WorkoutTemplate::factory()->create();

    livewire(ListWeeklySchedules::class)
        ->call('selectEmptyDay', 1)
        ->setActionData([
            'workout_template_id' => $template->id,
            'is_active' => true,
        ])
        ->callMountedAction()
        ->assertNotified('Schedule Created');

    expect(WeeklySchedule::where('day_of_week', 1)->first())
        ->not->toBeNull()
        ->workout_template_id->toBe($template->id)
        ->is_active->toBeTrue();
});

it('can create an inactive schedule', function () {
    $template = WorkoutTemplate::factory()->create();

    livewire(ListWeeklySchedules::class)
        ->call('selectEmptyDay', 5)
        ->setActionData([
            'workout_template_id' => $template->id,
            'is_active' => false,
        ])
        ->callMountedAction();

    expect(WeeklySchedule::where('day_of_week', 5)->first())
        ->not->toBeNull()
        ->is_active->toBeFalse();
});

it('validates required workout template when creating', function () {
    livewire(ListWeeklySchedules::class)
        ->call('selectEmptyDay', 2)
        ->setActionData([
            'workout_template_id' => null,
        ])
        ->callMountedAction()
        ->assertHasActionErrors(['workout_template_id' => 'required']);
});

it('can edit a schedule via modal', function () {
    $oldTemplate = WorkoutTemplate::factory()->create();
    $newTemplate = WorkoutTemplate::factory()->create(['name' => 'New Workout']);
    $schedule = WeeklySchedule::factory()->forDay(3)->create([
        'workout_template_id' => $oldTemplate->id,
        'is_active' => true,
    ]);

    livewire(ListWeeklySchedules::class)
        ->call('selectDay', 3)
        ->callAction('editSchedule', [
            'workout_template_id' => $newTemplate->id,
            'is_active' => false,
        ])
        ->assertNotified('Schedule Updated');

    expect($schedule->fresh())
        ->workout_template_id->toBe($newTemplate->id)
        ->is_active->toBeFalse();
});

it('can delete a schedule via modal', function () {
    $schedule = WeeklySchedule::factory()->forDay(4)->create();

    livewire(ListWeeklySchedules::class)
        ->call('selectDay', 4)
        ->callAction('deleteSchedule')
        ->assertNotified('Schedule Deleted');

    expect(WeeklySchedule::find($schedule->id))->toBeNull();
});

it('can start a workout from scheduled day', function () {
    Carbon::setTestNow(Carbon::parse('2026-01-07 10:00:00')); // Wednesday

    $template = WorkoutTemplate::factory()->create(['name' => 'Test Workout']);
    WeeklySchedule::factory()->forDay(3)->create(['workout_template_id' => $template->id]); // Wednesday = 3

    livewire(ListWeeklySchedules::class)
        ->call('selectDay', 3)
        ->callAction('startWorkout')
        ->assertNotified('Workout Started')
        ->assertRedirect('/');

    expect(Workout::where('workout_template_id', $template->id)->first())
        ->not->toBeNull()
        ->status->toBe(WorkoutStatus::InProgress);

    Carbon::setTestNow();
});

it('creates workout with correct date when starting from different day', function () {
    Carbon::setTestNow(Carbon::parse('2026-01-07')); // Wednesday

    $template = WorkoutTemplate::factory()->create();
    WeeklySchedule::factory()->forDay(1)->create(['workout_template_id' => $template->id]); // Monday

    livewire(ListWeeklySchedules::class)
        ->call('selectDay', 1)
        ->callAction('startWorkout');

    $workout = Workout::first();
    expect($workout->scheduled_date->format('Y-m-d'))->toBe('2026-01-05'); // Previous Monday

    Carbon::setTestNow();
});

it('suggests templates not already scheduled', function () {
    $usedTemplate = WorkoutTemplate::factory()->create(['name' => 'Used Template']);
    $unusedTemplate = WorkoutTemplate::factory()->create(['name' => 'Unused Template']);

    WeeklySchedule::factory()->forDay(1)->create(['workout_template_id' => $usedTemplate->id]);

    $page = livewire(ListWeeklySchedules::class);

    $suggestedId = $page->instance()->getSuggestedTemplateId(2);

    expect($suggestedId)->toBe($unusedTemplate->id);
});

it('returns null when all templates are scheduled', function () {
    $template = WorkoutTemplate::factory()->create();
    WeeklySchedule::factory()->forDay(1)->create(['workout_template_id' => $template->id]);

    $page = livewire(ListWeeklySchedules::class);

    $suggestedId = $page->instance()->getSuggestedTemplateId(2);

    expect($suggestedId)->toBeNull();
});

it('returns days ordered monday to sunday', function () {
    $page = livewire(ListWeeklySchedules::class);

    $orderedDays = $page->instance()->getOrderedDays();

    expect($orderedDays)->toBe([1, 2, 3, 4, 5, 6, 0]);
});

it('returns correct day name for each day', function () {
    $page = livewire(ListWeeklySchedules::class);

    expect($page->instance()->getDayName(0))->toBe('Sunday');
    expect($page->instance()->getDayName(1))->toBe('Monday');
    expect($page->instance()->getDayName(2))->toBe('Tuesday');
    expect($page->instance()->getDayName(3))->toBe('Wednesday');
    expect($page->instance()->getDayName(4))->toBe('Thursday');
    expect($page->instance()->getDayName(5))->toBe('Friday');
    expect($page->instance()->getDayName(6))->toBe('Saturday');
});

it('calculates correct date for day of week', function () {
    Carbon::setTestNow(Carbon::parse('2026-01-07')); // Wednesday

    $page = livewire(ListWeeklySchedules::class);

    // Wednesday (today)
    expect($page->instance()->getDateForDayOfWeek(3)->format('Y-m-d'))->toBe('2026-01-07');

    // Monday (2 days ago)
    expect($page->instance()->getDateForDayOfWeek(1)->format('Y-m-d'))->toBe('2026-01-05');

    // Friday (2 days ahead)
    expect($page->instance()->getDateForDayOfWeek(5)->format('Y-m-d'))->toBe('2026-01-09');

    // Sunday (3 days ago)
    expect($page->instance()->getDateForDayOfWeek(0)->format('Y-m-d'))->toBe('2026-01-04');

    Carbon::setTestNow();
});

it('loads schedules keyed by day of week', function () {
    $mondaySchedule = WeeklySchedule::factory()->forDay(1)->create();
    $fridaySchedule = WeeklySchedule::factory()->forDay(5)->create();

    $page = livewire(ListWeeklySchedules::class);

    expect($page->instance()->getScheduleForDay(1)?->id)->toBe($mondaySchedule->id);
    expect($page->instance()->getScheduleForDay(5)?->id)->toBe($fridaySchedule->id);
    expect($page->instance()->getScheduleForDay(3))->toBeNull();
});

it('reloads schedules after creating', function () {
    $template = WorkoutTemplate::factory()->create();

    $page = livewire(ListWeeklySchedules::class);

    expect($page->instance()->getScheduleForDay(2))->toBeNull();

    $page->call('selectEmptyDay', 2)
        ->setActionData([
            'workout_template_id' => $template->id,
            'is_active' => true,
        ])
        ->callMountedAction();

    expect($page->instance()->getScheduleForDay(2))->not->toBeNull();
});

it('reloads schedules after deleting', function () {
    $schedule = WeeklySchedule::factory()->forDay(4)->create();

    $page = livewire(ListWeeklySchedules::class);

    expect($page->instance()->getScheduleForDay(4))->not->toBeNull();

    $page->call('selectDay', 4)
        ->callAction('deleteSchedule');

    expect($page->instance()->getScheduleForDay(4))->toBeNull();
});

it('has correct navigation icon', function () {
    expect(WeeklyScheduleResource::getNavigationIcon())->not->toBeNull();
});

it('view modal shows exercise details', function () {
    $template = WorkoutTemplate::factory()
        ->hasWorkoutTemplateExercises(2)
        ->create(['name' => 'Workout With Exercises']);
    WeeklySchedule::factory()->forDay(1)->create([
        'workout_template_id' => $template->id,
        'is_active' => true,
    ]);

    livewire(ListWeeklySchedules::class)
        ->call('selectDay', 1)
        ->assertMountedActionModalSee('Workout With Exercises')
        ->assertMountedActionModalSee('Active');
});
