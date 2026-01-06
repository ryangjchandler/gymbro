<?php

use App\Enums\PersonalRecordType;
use App\Filament\Widgets\RecentPersonalRecordsWidget;
use App\Models\Exercise;
use App\Models\PersonalRecord;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders the recent personal records widget', function () {
    livewire(RecentPersonalRecordsWidget::class)
        ->assertOk()
        ->assertSee('Recent Personal Records');
});

it('displays recent personal records', function () {
    $exercise = Exercise::factory()->create(['name' => 'Bench Press']);
    $pr = PersonalRecord::factory()->create([
        'exercise_id' => $exercise->id,
        'type' => PersonalRecordType::MaxWeight,
        'value' => 100,
        'achieved_at' => now(),
    ]);

    livewire(RecentPersonalRecordsWidget::class)
        ->assertOk()
        ->assertSee('Bench Press')
        ->assertSee('Max Weight')
        ->assertSee('100.0kg');
});

it('shows empty state when no personal records exist', function () {
    livewire(RecentPersonalRecordsWidget::class)
        ->assertOk()
        ->assertSee('Recent Personal Records');
});

it('limits results to 5 records', function () {
    // Create 7 PRs
    PersonalRecord::factory()->count(7)->create([
        'achieved_at' => now(),
    ]);

    $widget = livewire(RecentPersonalRecordsWidget::class);

    // Table should only show 5 records
    $widget->assertOk();
});

it('orders records by achieved_at descending', function () {
    $exercise = Exercise::factory()->create();

    $older = PersonalRecord::factory()->create([
        'exercise_id' => $exercise->id,
        'achieved_at' => now()->subDays(5),
        'value' => 50,
    ]);

    $newer = PersonalRecord::factory()->create([
        'exercise_id' => $exercise->id,
        'achieved_at' => now(),
        'value' => 100,
    ]);

    livewire(RecentPersonalRecordsWidget::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$newer, $older]);
});

it('displays max reps records correctly', function () {
    $exercise = Exercise::factory()->create(['name' => 'Pull Ups']);
    PersonalRecord::factory()->create([
        'exercise_id' => $exercise->id,
        'type' => PersonalRecordType::MaxReps,
        'value' => 15,
        'achieved_at' => now(),
    ]);

    livewire(RecentPersonalRecordsWidget::class)
        ->assertOk()
        ->assertSee('Pull Ups')
        ->assertSee('Max Reps')
        ->assertSee('15 reps');
});

it('displays max volume records correctly', function () {
    $exercise = Exercise::factory()->create(['name' => 'Squat']);
    PersonalRecord::factory()->create([
        'exercise_id' => $exercise->id,
        'type' => PersonalRecordType::MaxVolume,
        'value' => 5000,
        'achieved_at' => now(),
    ]);

    livewire(RecentPersonalRecordsWidget::class)
        ->assertOk()
        ->assertSee('Squat')
        ->assertSee('Max Volume')
        ->assertSee('5,000kg');
});

it('has responsive column span', function () {
    $widget = new RecentPersonalRecordsWidget;
    expect($widget->getColumnSpan())->toBe([
        'default' => 'full',
        'lg' => 2,
    ]);
});

it('has correct sort order', function () {
    expect(RecentPersonalRecordsWidget::getSort())->toBe(4);
});

it('is not paginated', function () {
    // Create more than 5 records to verify no pagination
    PersonalRecord::factory()->count(10)->create([
        'achieved_at' => now(),
    ]);

    livewire(RecentPersonalRecordsWidget::class)
        ->assertOk();
});
