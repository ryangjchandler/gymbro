<?php

namespace App\Filament\Admin\Fields;

use Filament\Forms\Components\TextInput;

class SetsInput
{
    public static function make(string $name = 'target_sets'): TextInput
    {
        return TextInput::make($name)
            ->label('Sets')
            ->numeric()
            ->minValue(1)
            ->maxValue(20)
            ->step(1)
            ->default(3);
    }
}
