<?php

namespace App\Filament\Pages;

use App\Enums\AchievementCategory;
use App\Services\AchievementService;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class Achievements extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Trophy;

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.achievements';

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public function getCategories(): array
    {
        return AchievementCategory::cases();
    }

    public function getAchievementsForCategory(AchievementCategory $category): Collection
    {
        return app(AchievementService::class)->getForCategory($category);
    }

    public function getUnlockedCount(): int
    {
        return app(AchievementService::class)->getUnlocked()->count();
    }

    public function getTotalCount(): int
    {
        return count(\App\Enums\Achievement::cases());
    }
}
