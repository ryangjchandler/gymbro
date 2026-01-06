<?php

namespace App\Filament\Resources\PersonalRecords\Schemas;

use App\Enums\PersonalRecordType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PersonalRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Record')
                    ->schema([
                        Select::make('exercise_id')
                            ->relationship('exercise', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('type')
                            ->options(PersonalRecordType::class)
                            ->required(),
                        TextInput::make('value')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText('Enter weight in kg for Max Weight/Volume, or number for Max Reps'),
                        DatePicker::make('achieved_at')
                            ->label('Date Achieved')
                            ->required()
                            ->default(today()),
                    ]),
            ]);
    }
}
