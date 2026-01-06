<?php

namespace App\Filament\Admin\Fields;

use Filament\Forms\Components\TextInput;

class WeightInput
{
    public static function make(string $name = 'weight_kg'): TextInput
    {
        return TextInput::make($name)
            ->label('Weight (kg)')
            ->numeric()
            ->minValue(0)
            ->maxValue(999)
            ->step(0.5)
            ->suffix('kg');
    }
}
