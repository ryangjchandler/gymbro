<?php

namespace App\Filament\Resources\WorkoutTemplates;

use App\Filament\Resources\WorkoutTemplates\Pages\CreateWorkoutTemplate;
use App\Filament\Resources\WorkoutTemplates\Pages\EditWorkoutTemplate;
use App\Filament\Resources\WorkoutTemplates\Pages\ListWorkoutTemplates;
use App\Filament\Resources\WorkoutTemplates\Pages\ViewWorkoutTemplate;
use App\Filament\Resources\WorkoutTemplates\Schemas\WorkoutTemplateForm;
use App\Filament\Resources\WorkoutTemplates\Schemas\WorkoutTemplateInfolist;
use App\Filament\Resources\WorkoutTemplates\Tables\WorkoutTemplatesTable;
use App\Models\WorkoutTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkoutTemplateResource extends Resource
{
    protected static ?string $model = WorkoutTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return WorkoutTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkoutTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkoutTemplatesTable::configure($table);
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
            'index' => ListWorkoutTemplates::route('/'),
            'create' => CreateWorkoutTemplate::route('/create'),
            'view' => ViewWorkoutTemplate::route('/{record}'),
            'edit' => EditWorkoutTemplate::route('/{record}/edit'),
        ];
    }
}
