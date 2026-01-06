<?php

namespace App\Enums;

enum AchievementTier: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';
    case Platinum = 'platinum';
    case Ruby = 'ruby';
    case Diamond = 'diamond';

    public function getLabel(): string
    {
        return match ($this) {
            self::Bronze => 'Bronze',
            self::Silver => 'Silver',
            self::Gold => 'Gold',
            self::Platinum => 'Platinum',
            self::Ruby => 'Ruby',
            self::Diamond => 'Diamond',
        };
    }

    public function getIconColor(): string
    {
        return match ($this) {
            self::Bronze => 'text-amber-600',
            self::Silver => 'text-gray-400',
            self::Gold => 'text-yellow-500',
            self::Platinum => 'text-cyan-400',
            self::Ruby => 'text-red-500',
            self::Diamond => 'text-purple-500',
        };
    }

    public function getBackgroundColor(): string
    {
        return match ($this) {
            self::Bronze => 'bg-amber-100 dark:bg-amber-900/30',
            self::Silver => 'bg-gray-100 dark:bg-gray-800/50',
            self::Gold => 'bg-yellow-100 dark:bg-yellow-900/30',
            self::Platinum => 'bg-cyan-100 dark:bg-cyan-900/30',
            self::Ruby => 'bg-red-100 dark:bg-red-900/30',
            self::Diamond => 'bg-purple-100 dark:bg-purple-900/30',
        };
    }

    public function getBorderColor(): string
    {
        return match ($this) {
            self::Bronze => 'border-amber-300',
            self::Silver => 'border-gray-300',
            self::Gold => 'border-yellow-300',
            self::Platinum => 'border-cyan-300',
            self::Ruby => 'border-red-300',
            self::Diamond => 'border-purple-300',
        };
    }
}
