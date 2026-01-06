<?php

namespace App\Filament\Resources\Exercises\Schemas;

use App\Filament\Admin\Fields\ExerciseTypeSelect;
use App\Filament\Admin\Fields\MuscleGroupSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExerciseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Exercise Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        MuscleGroupSelect::make()
                            ->required(),
                        ExerciseTypeSelect::make()
                            ->required(),
                        Textarea::make('instructions')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
