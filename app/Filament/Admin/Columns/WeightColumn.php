<?php

namespace App\Filament\Admin\Columns;

use Filament\Tables\Columns\TextColumn;

class WeightColumn
{
    public static function make(string $name = 'weight_kg'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Weight')
            ->suffix(' kg')
            ->sortable();
    }
}
