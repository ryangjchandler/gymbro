<?php

namespace App\Filament\Resources\PersonalRecords;

use App\Filament\Resources\PersonalRecords\Pages\CreatePersonalRecord;
use App\Filament\Resources\PersonalRecords\Pages\EditPersonalRecord;
use App\Filament\Resources\PersonalRecords\Pages\ListPersonalRecords;
use App\Filament\Resources\PersonalRecords\Pages\ViewPersonalRecord;
use App\Filament\Resources\PersonalRecords\Schemas\PersonalRecordForm;
use App\Filament\Resources\PersonalRecords\Schemas\PersonalRecordInfolist;
use App\Filament\Resources\PersonalRecords\Tables\PersonalRecordsTable;
use App\Models\PersonalRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PersonalRecordResource extends Resource
{
    protected static ?string $model = PersonalRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    public static function form(Schema $schema): Schema
    {
        return PersonalRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PersonalRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PersonalRecordsTable::configure($table);
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
            'index' => ListPersonalRecords::route('/'),
            'create' => CreatePersonalRecord::route('/create'),
            'view' => ViewPersonalRecord::route('/{record}'),
            'edit' => EditPersonalRecord::route('/{record}/edit'),
        ];
    }
}
