<?php

namespace App\Filament\Admin\Columns;

use Filament\Tables\Columns\TextColumn;

class ExerciseTypeColumn
{
    public static function make(string $name = 'type'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Type')
            ->badge()
            ->sortable();
    }
}
