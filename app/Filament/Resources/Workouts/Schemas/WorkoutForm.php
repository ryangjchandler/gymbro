<?php

namespace App\Filament\Resources\Workouts\Schemas;

use App\Enums\WorkoutStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkoutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Workout')
                    ->schema([
                        DatePicker::make('scheduled_date')
                            ->label('Date')
                            ->required()
                            ->default(today()),
                        Select::make('workout_template_id')
                            ->label('Workout Template')
                            ->relationship('workoutTemplate', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->options(WorkoutStatus::class)
                            ->default(WorkoutStatus::Pending)
                            ->required(),
                    ]),
                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->hiddenLabel()
                            ->rows(3),
                    ])
                    ->collapsed(),
            ]);
    }
}
