<?php

namespace App\Filament\Widgets;

use App\Actions\Workouts\CompleteWorkout;
use App\Actions\Workouts\SkipWorkout;
use App\Livewire\RestTimer;
use App\Models\Workout;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;

class ActiveWorkoutWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected static ?int $sort = 1;

    protected string $view = 'filament.widgets.active-workout-widget';

    protected int|string|array $columnSpan = 'full';

    public ?Workout $activeWorkout = null;

    protected static bool $isLazy = false;

    public function mount(): void
    {
        $this->loadActiveWorkout();
    }

    public function loadActiveWorkout(): void
    {
        $this->activeWorkout = Workout::query()
            ->inProgress()
            ->with([
                'workoutTemplate.workoutTemplateExercises.exercise',
                'workoutSets.exercise',
            ])
            ->latest('started_at')
            ->first();
    }

    public static function canView(): bool
    {
        return Workout::query()->inProgress()->exists();
    }

    public function completeWorkoutAction(): Action
    {
        return Action::make('completeWorkout')
            ->label('Complete Workout')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation()
            ->modalHeading('Complete Workout')
            ->modalDescription('Are you sure you want to mark this workout as complete?')
            ->action(function (CompleteWorkout $completeWorkout) {
                $completeWorkout->handle($this->activeWorkout);
                $this->loadActiveWorkout();
            });
    }

    public function skipWorkoutAction(): Action
    {
        return Action::make('skipWorkout')
            ->label('Skip')
            ->color('gray')
            ->icon('heroicon-o-forward')
            ->requiresConfirmation()
            ->modalHeading('Skip Workout')
            ->modalDescription('Are you sure you want to skip this workout?')
            ->action(function (SkipWorkout $skipWorkout) {
                $skipWorkout->handle($this->activeWorkout);
                $this->loadActiveWorkout();
            });
    }

    public function getRestTimerComponent(): string
    {
        return RestTimer::class;
    }

    public function getDefaultRestSeconds(): int
    {
        return RestTimer::DEFAULT_REST_SECONDS;
    }
}
