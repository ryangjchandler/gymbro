<?php

namespace App\Filament\Resources\Exercises\Schemas;

use App\Filament\Admin\Fields\ExerciseTypeSelect;
use App\Filament\Admin\Fields\MuscleGroupSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                        Toggle::make('is_pinned')
                            ->label('Pin to Dashboard')
                            ->helperText('Pinned exercises appear in the Estimated 1RM widget'),
                        Textarea::make('instructions')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
