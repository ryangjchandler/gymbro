<?php

namespace App\Filament\Admin\Fields;

use Filament\Forms\Components\TextInput;

class RepsInput
{
    public static function make(string $name = 'reps'): TextInput
    {
        return TextInput::make($name)
            ->label('Reps')
            ->numeric()
            ->minValue(1)
            ->maxValue(999)
            ->step(1);
    }
}
