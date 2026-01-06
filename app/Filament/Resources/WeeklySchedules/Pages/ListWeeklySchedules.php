<?php

namespace App\Filament\Resources\WeeklySchedules\Pages;

use App\Actions\WeeklySchedules\CreateWeeklySchedule;
use App\Actions\WeeklySchedules\DeleteWeeklySchedule;
use App\Actions\WeeklySchedules\UpdateWeeklySchedule;
use App\Actions\Workouts\CreateWorkoutFromSchedule;
use App\Filament\Resources\WeeklySchedules\WeeklyScheduleResource;
use App\Models\WeeklySchedule;
use App\Models\WorkoutTemplate;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Width;
use Illuminate\Support\Collection;

class ListWeeklySchedules extends Page implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected static string $resource = WeeklyScheduleResource::class;

    protected string $view = 'filament.resources.weekly-schedules.pages.list-weekly-schedules';

    public Collection $schedules;

    public ?int $selectedDay = null;

    public function mount(): void
    {
        $this->loadSchedules();
    }

    public function loadSchedules(): void
    {
        $this->schedules = WeeklySchedule::query()
            ->with(['workoutTemplate.workoutTemplateExercises.exercise'])
            ->get()
            ->keyBy('day_of_week');
    }

    /**
     * Get days ordered Monday to Sunday.
     *
     * @return array<int>
     */
    public function getOrderedDays(): array
    {
        return [1, 2, 3, 4, 5, 6, 0];
    }

    public function getScheduleForDay(int $day): ?WeeklySchedule
    {
        return $this->schedules->get($day);
    }

    public function getDayName(int $day): string
    {
        return match ($day) {
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            default => 'Unknown',
        };
    }

    public function getDateForDayOfWeek(int $targetDay): Carbon
    {
        $today = now()->startOfDay();
        $currentDay = $today->dayOfWeek;
        $diff = $targetDay - $currentDay;

        return $today->copy()->addDays($diff);
    }

    public function getSuggestedTemplateId(int $dayOfWeek): ?int
    {
        $usedTemplateIds = $this->schedules
            ->where('day_of_week', '!=', $dayOfWeek)
            ->pluck('workout_template_id')
            ->unique()
            ->toArray();

        $suggested = WorkoutTemplate::query()
            ->whereNotIn('id', $usedTemplateIds)
            ->first();

        return $suggested?->id;
    }

    public function selectDay(int $day): void
    {
        $this->selectedDay = $day;
        $this->mountAction('viewSchedule');
    }

    public function selectEmptyDay(int $day): void
    {
        $this->selectedDay = $day;
        $this->mountAction('createSchedule');
    }

    public function viewScheduleAction(): Action
    {
        return Action::make('viewSchedule')
            ->modalHeading(fn () => $this->getDayName($this->selectedDay ?? 0))
            ->modalWidth(Width::TwoExtraLarge)
            ->modalContent(function () {
                $schedule = $this->getScheduleForDay($this->selectedDay);
                if (! $schedule) {
                    return null;
                }

                return view('filament.resources.weekly-schedules.pages.view-schedule-modal', [
                    'schedule' => $schedule,
                ]);
            })
            ->modalFooterActions(fn () => [
                $this->startWorkoutAction(),
                $this->editScheduleAction(),
                $this->deleteScheduleAction(),
            ])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function createScheduleAction(): Action
    {
        return Action::make('createSchedule')
            ->modalHeading(fn () => 'Schedule Workout - '.$this->getDayName($this->selectedDay ?? 0))
            ->form([
                Select::make('workout_template_id')
                    ->label('Workout Template')
                    ->options(WorkoutTemplate::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn () => $this->getSuggestedTemplateId($this->selectedDay ?? 0)),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])
            ->action(function (array $data, CreateWeeklySchedule $createWeeklySchedule): void {
                $createWeeklySchedule->handle(
                    dayOfWeek: $this->selectedDay,
                    workoutTemplateId: $data['workout_template_id'],
                    isActive: $data['is_active'],
                );

                $this->loadSchedules();

                Notification::make()
                    ->title('Schedule Created')
                    ->body("Workout scheduled for {$this->getDayName($this->selectedDay)}.")
                    ->success()
                    ->send();
            })
            ->modalSubmitActionLabel('Create Schedule');
    }

    public function editScheduleAction(): Action
    {
        return Action::make('editSchedule')
            ->label('Edit')
            ->color('gray')
            ->form([
                Select::make('workout_template_id')
                    ->label('Workout Template')
                    ->options(WorkoutTemplate::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn () => $this->getScheduleForDay($this->selectedDay)?->workout_template_id),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(fn () => $this->getScheduleForDay($this->selectedDay)?->is_active ?? true),
            ])
            ->action(function (array $data, UpdateWeeklySchedule $updateWeeklySchedule): void {
                $schedule = $this->getScheduleForDay($this->selectedDay);
                if (! $schedule) {
                    return;
                }

                $updateWeeklySchedule->handle(
                    schedule: $schedule,
                    workoutTemplateId: $data['workout_template_id'],
                    isActive: $data['is_active'],
                );

                $this->loadSchedules();

                Notification::make()
                    ->title('Schedule Updated')
                    ->success()
                    ->send();
            })
            ->modalSubmitActionLabel('Save Changes');
    }

    public function deleteScheduleAction(): Action
    {
        return Action::make('deleteSchedule')
            ->label('Delete')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Delete Schedule')
            ->modalDescription(fn () => "Are you sure you want to remove the workout scheduled for {$this->getDayName($this->selectedDay)}?")
            ->action(function (DeleteWeeklySchedule $deleteWeeklySchedule): void {
                $schedule = $this->getScheduleForDay($this->selectedDay);
                if (! $schedule) {
                    return;
                }

                $deleteWeeklySchedule->handle($schedule);
                $this->loadSchedules();

                Notification::make()
                    ->title('Schedule Deleted')
                    ->success()
                    ->send();
            });
    }

    public function startWorkoutAction(): Action
    {
        return Action::make('startWorkout')
            ->label('Start Workout')
            ->color('success')
            ->icon('heroicon-o-play')
            ->requiresConfirmation()
            ->modalHeading('Start Workout')
            ->modalDescription(function () {
                $schedule = $this->getScheduleForDay($this->selectedDay);
                $date = $this->getDateForDayOfWeek($this->selectedDay);
                $dateStr = $date->isToday() ? 'today' : $date->format('l, M j');

                return "Start '{$schedule?->workoutTemplate?->name}' workout for {$dateStr}?";
            })
            ->action(function (CreateWorkoutFromSchedule $createWorkoutFromSchedule): void {
                $schedule = $this->getScheduleForDay($this->selectedDay);
                if (! $schedule) {
                    return;
                }

                $date = $this->getDateForDayOfWeek($this->selectedDay);

                $createWorkoutFromSchedule->handle($schedule, $date);

                Notification::make()
                    ->title('Workout Started')
                    ->body('Your workout has begun. Good luck!')
                    ->success()
                    ->send();

                $this->redirect('/');
            });
    }
}
