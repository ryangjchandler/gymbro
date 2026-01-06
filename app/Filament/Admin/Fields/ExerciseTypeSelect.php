<?php

namespace App\Filament\Admin\Fields;

use App\Enums\ExerciseType;
use Filament\Forms\Components\Select;

class ExerciseTypeSelect
{
    public static function make(string $name = 'type'): Select
    {
        return Select::make($name)
            ->label('Type')
            ->options(ExerciseType::class);
    }
}
