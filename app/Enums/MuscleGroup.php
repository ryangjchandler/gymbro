<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MuscleGroup: string implements HasColor, HasLabel
{
    case Biceps = 'biceps';
    case Triceps = 'triceps';
    case Chest = 'chest';
    case Back = 'back';
    case Shoulders = 'shoulders';
    case Quads = 'quads';
    case Hamstrings = 'hamstrings';
    case Glutes = 'glutes';
    case Core = 'core';
    case Cardio = 'cardio';

    public function getLabel(): string
    {
        return match ($this) {
            self::Biceps => 'Biceps',
            self::Triceps => 'Triceps',
            self::Chest => 'Chest',
            self::Back => 'Back',
            self::Shoulders => 'Shoulders',
            self::Quads => 'Quads',
            self::Hamstrings => 'Hamstrings',
            self::Glutes => 'Glutes',
            self::Core => 'Core',
            self::Cardio => 'Cardio',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Biceps => 'info',
            self::Triceps => 'info',
            self::Chest => 'success',
            self::Back => 'success',
            self::Shoulders => 'warning',
            self::Quads => 'danger',
            self::Hamstrings => 'danger',
            self::Glutes => 'danger',
            self::Core => 'gray',
            self::Cardio => 'primary',
        };
    }
}
