<?php

namespace App\Filament\Widgets;

use App\Services\AchievementService;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class AlmostAchievedWidget extends Widget
{
    protected string $view = 'filament.widgets.almost-achieved-widget';

    protected static ?string $heading = 'Almost There!';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    protected ?string $pollingInterval = null;

    public function getAlmostAchieved(): Collection
    {
        return app(AchievementService::class)->getAlmostAchieved(5);
    }
}
