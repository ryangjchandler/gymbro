<?php

namespace App\Filament\Resources\WeeklySchedules;

use App\Filament\Resources\WeeklySchedules\Pages\ListWeeklySchedules;
use App\Filament\Resources\WeeklySchedules\Schemas\WeeklyScheduleForm;
use App\Models\WeeklySchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class WeeklyScheduleResource extends Resource
{
    protected static ?string $model = WeeklySchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Weekly Schedule';

    protected static ?string $pluralModelLabel = 'Weekly Schedule';

    public static function form(Schema $schema): Schema
    {
        return WeeklyScheduleForm::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWeeklySchedules::route('/'),
        ];
    }
}
