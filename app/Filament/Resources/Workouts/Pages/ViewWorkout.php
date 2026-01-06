<?php

namespace App\Filament\Resources\Workouts\Pages;

use App\Actions\Workouts\StartWorkout;
use App\Enums\WorkoutStatus;
use App\Filament\Resources\Workouts\WorkoutResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkout extends ViewRecord
{
    protected static string $resource = WorkoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('start')
                ->label('Start Workout')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === WorkoutStatus::Pending)
                ->requiresConfirmation()
                ->modalHeading('Start Workout')
                ->modalDescription(fn (): string => "Start '{$this->record->workoutTemplate?->name}' workout now?")
                ->action(function (StartWorkout $startWorkout): void {
                    $startWorkout->handle($this->record);

                    Notification::make()
                        ->title('Workout Started')
                        ->body('Your workout has begun. Good luck!')
                        ->success()
                        ->send();

                    $this->redirect(route('filament.admin.pages.dashboard'));
                }),
            EditAction::make(),
        ];
    }
}
