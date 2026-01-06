<?php

namespace App\Filament\Admin\Columns;

use Filament\Tables\Columns\TextColumn;

class RepsColumn
{
    public static function make(string $name = 'reps'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Reps')
            ->sortable();
    }
}
