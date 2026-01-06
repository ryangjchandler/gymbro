<?php

namespace App\Filament\Resources\BodyWeights\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BodyWeightInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('recorded_at')
                    ->label('Date')
                    ->date(),
                TextEntry::make('formattedWeight')
                    ->label('Weight'),
                TextEntry::make('totalKilograms')
                    ->label('Weight (kg)')
                    ->formatStateUsing(fn ($state): string => "{$state} kg"),
                ImageEntry::make('photo_path')
                    ->label('Progress Photo')
                    ->visibility('private')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->placeholder('No notes')
                    ->columnSpanFull(),
            ]);
    }
}
