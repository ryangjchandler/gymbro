<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PersonalRecordType: string implements HasColor, HasLabel
{
    case MaxWeight = 'max_weight';
    case MaxReps = 'max_reps';
    case MaxVolume = 'max_volume';

    public function getLabel(): string
    {
        return match ($this) {
            self::MaxWeight => 'Max Weight',
            self::MaxReps => 'Max Reps',
            self::MaxVolume => 'Max Volume',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::MaxWeight => 'success',
            self::MaxReps => 'info',
            self::MaxVolume => 'warning',
        };
    }
}
