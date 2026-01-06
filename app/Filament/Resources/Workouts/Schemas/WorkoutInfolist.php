<?php

namespace App\Filament\Resources\Workouts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WorkoutInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('scheduled_date')
                    ->label('Date')
                    ->date(),
                TextEntry::make('workoutTemplate.name')
                    ->label('Workout Template'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('started_at')
                    ->label('Started')
                    ->dateTime()
                    ->placeholder('Not started'),
                TextEntry::make('completed_at')
                    ->label('Completed')
                    ->dateTime()
                    ->placeholder('Not completed'),
                TextEntry::make('durationInMinutes')
                    ->label('Duration')
                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state} min" : '-')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('No notes')
                    ->columnSpanFull(),
            ]);
    }
}
