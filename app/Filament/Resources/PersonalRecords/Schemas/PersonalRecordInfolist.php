<?php

namespace App\Filament\Resources\PersonalRecords\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PersonalRecordInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('exercise.name')
                    ->label('Exercise'),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('formattedValue')
                    ->label('Value'),
                TextEntry::make('achieved_at')
                    ->label('Date Achieved')
                    ->date(),
                TextEntry::make('workoutSet.id')
                    ->label('Workout Set')
                    ->placeholder('Manual entry')
                    ->formatStateUsing(fn ($state): string => $state ? "Set #{$state}" : 'Manual entry'),
            ]);
    }
}
