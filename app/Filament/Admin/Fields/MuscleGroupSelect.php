<?php

namespace App\Filament\Admin\Fields;

use App\Enums\MuscleGroup;
use Filament\Forms\Components\Select;

class MuscleGroupSelect
{
    public static function make(string $name = 'muscle_group'): Select
    {
        return Select::make($name)
            ->label('Muscle Group')
            ->options(MuscleGroup::class)
            ->searchable();
    }
}
