<?php

namespace App\Filament\Admin\Columns;

use Filament\Tables\Columns\TextColumn;

class WorkoutStatusColumn
{
    public static function make(string $name = 'status'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Status')
            ->badge()
            ->sortable();
    }
}
