<?php

namespace App\Filament\Resources\BodyWeights;

use App\Filament\Resources\BodyWeights\Pages\CreateBodyWeight;
use App\Filament\Resources\BodyWeights\Pages\EditBodyWeight;
use App\Filament\Resources\BodyWeights\Pages\ListBodyWeights;
use App\Filament\Resources\BodyWeights\Pages\ViewBodyWeight;
use App\Filament\Resources\BodyWeights\Schemas\BodyWeightForm;
use App\Filament\Resources\BodyWeights\Schemas\BodyWeightInfolist;
use App\Filament\Resources\BodyWeights\Tables\BodyWeightsTable;
use App\Models\BodyWeight;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BodyWeightResource extends Resource
{
    protected static ?string $model = BodyWeight::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $navigationLabel = 'Body Weight';

    protected static ?string $pluralModelLabel = 'Body Weight';

    public static function form(Schema $schema): Schema
    {
        return BodyWeightForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BodyWeightInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BodyWeightsTable::configure($table);
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
            'index' => ListBodyWeights::route('/'),
            'create' => CreateBodyWeight::route('/create'),
            'view' => ViewBodyWeight::route('/{record}'),
            'edit' => EditBodyWeight::route('/{record}/edit'),
        ];
    }
}
