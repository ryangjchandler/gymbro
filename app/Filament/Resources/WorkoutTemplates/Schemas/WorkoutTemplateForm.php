<?php

namespace App\Filament\Resources\WorkoutTemplates\Schemas;

use App\Filament\Admin\Fields\DurationSecondsInput;
use App\Filament\Admin\Fields\RepsInput;
use App\Filament\Admin\Fields\RestSecondsInput;
use App\Filament\Admin\Fields\SetsInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkoutTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3),
                    ]),
                Section::make('Exercises')
                    ->schema([
                        Repeater::make('workoutTemplateExercises')
                            ->relationship()
                            ->orderColumn('order')
                            ->schema([
                                Select::make('exercise_id')
                                    ->relationship('exercise', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                SetsInput::make('target_sets')
                                    ->label('Sets'),
                                RepsInput::make('target_reps')
                                    ->label('Reps'),
                                DurationSecondsInput::make('target_duration_seconds')
                                    ->label('Duration'),
                                RestSecondsInput::make()
                                    ->label('Rest'),
                                Textarea::make('notes')
                                    ->rows(2),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['exercise_id']
                                ? \App\Models\Exercise::find($state['exercise_id'])?->name
                                : null
                            )
                            ->collapsible()
                            ->cloneable(),
                    ]),
            ]);
    }
}
