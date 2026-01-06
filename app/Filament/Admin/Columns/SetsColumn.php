<?php

namespace App\Filament\Admin\Columns;

use Filament\Tables\Columns\TextColumn;

class SetsColumn
{
    public static function make(string $name = 'sets'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Sets')
            ->sortable();
    }
}
