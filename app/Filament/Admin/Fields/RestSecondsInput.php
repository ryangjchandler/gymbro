<?php

namespace App\Filament\Admin\Fields;

use Filament\Forms\Components\TextInput;

class RestSecondsInput
{
    public static function make(string $name = 'rest_seconds'): TextInput
    {
        return TextInput::make($name)
            ->label('Rest')
            ->numeric()
            ->minValue(0)
            ->maxValue(300)
            ->step(5)
            ->default(45)
            ->suffix('sec');
    }
}
