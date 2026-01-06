<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ExerciseType: string implements HasColor, HasLabel
{
    case Strength = 'strength';
    case Cardio = 'cardio';
    case Timed = 'timed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Strength => 'Strength',
            self::Cardio => 'Cardio',
            self::Timed => 'Timed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Strength => 'success',
            self::Cardio => 'primary',
            self::Timed => 'warning',
        };
    }
}
