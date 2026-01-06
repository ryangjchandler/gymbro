<?php

namespace App\Filament\Admin\Columns;

use Filament\Tables\Columns\TextColumn;

class MuscleGroupColumn
{
    public static function make(string $name = 'muscle_group'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Muscle Group')
            ->badge()
            ->sortable();
    }
}
