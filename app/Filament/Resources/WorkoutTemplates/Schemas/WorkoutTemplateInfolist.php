<?php

namespace App\Filament\Resources\WorkoutTemplates\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WorkoutTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                RepeatableEntry::make('workoutTemplateExercises')
                    ->label('Exercises')
                    ->schema([
                        TextEntry::make('exercise.name')
                            ->label('Exercise'),
                        TextEntry::make('target_sets')
                            ->label('Sets'),
                        TextEntry::make('target_reps')
                            ->label('Reps'),
                        TextEntry::make('target_duration_seconds')
                            ->label('Duration')
                            ->placeholder('-')
                            ->formatStateUsing(function (?int $state): string {
                                if ($state === null) {
                                    return '-';
                                }

                                $minutes = intdiv($state, 60);
                                $seconds = $state % 60;

                                if ($minutes === 0) {
                                    return "{$seconds}s";
                                }

                                return $seconds > 0
                                    ? "{$minutes}m {$seconds}s"
                                    : "{$minutes}m";
                            }),
                        TextEntry::make('rest_seconds')
                            ->label('Rest')
                            ->suffix(' sec'),
                    ])
                    ->columns(5)
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
