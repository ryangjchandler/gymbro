<?php

namespace App\Filament\Resources\Exercises\Tables;

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use App\Filament\Admin\Columns\ExerciseTypeColumn;
use App\Filament\Admin\Columns\MuscleGroupColumn;
use App\Models\Exercise;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExercisesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                MuscleGroupColumn::make(),
                ExerciseTypeColumn::make(),
                IconColumn::make('is_pinned')
                    ->label('Pinned')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('muscle_group')
                    ->options(MuscleGroup::class),
                SelectFilter::make('type')
                    ->options(ExerciseType::class),
            ])
            ->recordActions([
                Action::make('togglePin')
                    ->label(fn (Exercise $record): string => $record->is_pinned ? 'Unpin' : 'Pin')
                    ->icon(fn (Exercise $record): Heroicon => $record->is_pinned ? Heroicon::Star : Heroicon::OutlinedStar)
                    ->color(fn (Exercise $record): string => $record->is_pinned ? 'warning' : 'gray')
                    ->action(function (Exercise $record): void {
                        if (! $record->is_pinned && Exercise::pinned()->count() >= 10) {
                            Notification::make()
                                ->title('Maximum pinned exercises reached')
                                ->body('You can only pin up to 10 exercises. Unpin one to pin another.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update(['is_pinned' => ! $record->is_pinned]);

                        Notification::make()
                            ->title($record->is_pinned ? 'Exercise pinned' : 'Exercise unpinned')
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
