<?php

namespace App\Filament\Admin\Fields;

use Filament\Forms\Components\TextInput;

class DurationSecondsInput
{
    public static function make(string $name = 'duration_seconds'): TextInput
    {
        return TextInput::make($name)
            ->label('Duration')
            ->numeric()
            ->minValue(0)
            ->step(1)
            ->suffix('sec');
    }
}
