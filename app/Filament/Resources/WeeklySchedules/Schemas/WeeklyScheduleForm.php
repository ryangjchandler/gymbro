<?php

namespace App\Filament\Resources\WeeklySchedules\Schemas;

use App\Models\WeeklySchedule;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WeeklyScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Schedule')
                    ->schema([
                        Select::make('day_of_week')
                            ->label('Day')
                            ->options(WeeklySchedule::getDayOptions())
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('workout_template_id')
                            ->label('Workout Template')
                            ->relationship('workoutTemplate', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }
}
