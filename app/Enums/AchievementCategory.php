<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum AchievementCategory: string implements HasIcon, HasLabel
{
    case Consistency = 'consistency';
    case Strength = 'strength';
    case Volume = 'volume';
    case Variety = 'variety';
    case BodyWeight = 'body_weight';

    public function getLabel(): string
    {
        return match ($this) {
            self::Consistency => 'Consistency',
            self::Strength => 'Strength',
            self::Volume => 'Volume',
            self::Variety => 'Variety',
            self::BodyWeight => 'Body Weight',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Consistency => Heroicon::Fire,
            self::Strength => Heroicon::Bolt,
            self::Volume => Heroicon::Scale,
            self::Variety => Heroicon::Sparkles,
            self::BodyWeight => Heroicon::ChartBar,
        };
    }
}
