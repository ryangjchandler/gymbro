<?php

namespace App\Filament\Resources\Workouts\Tables;

use App\Actions\Workouts\StartWorkout;
use App\Enums\WorkoutStatus;
use App\Models\Workout;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('workoutTemplate.name')
                    ->label('Workout')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->label('Started')
                    ->dateTime('H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('scheduled_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(WorkoutStatus::class),
            ])
            ->recordActions([
                Action::make('start')
                    ->label('Start')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (Workout $record): bool => $record->status === WorkoutStatus::Pending)
                    ->requiresConfirmation()
                    ->modalHeading('Start Workout')
                    ->modalDescription(fn (Workout $record): string => "Start '{$record->workoutTemplate?->name}' workout now?")
                    ->action(function (Workout $record, StartWorkout $startWorkout): void {
                        $startWorkout->handle($record);

                        Notification::make()
                            ->title('Workout Started')
                            ->body('Your workout has begun. Good luck!')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
