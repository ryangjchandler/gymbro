<?php

namespace App\Filament\Widgets;

use App\Models\BodyWeight;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BodyWeightWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            $this->getCurrentWeightStat(),
            $this->getWeightChangeStat(),
        ];
    }

    protected function getCurrentWeightStat(): Stat
    {
        $latest = BodyWeight::query()
            ->orderByDesc('recorded_at')
            ->first();

        if (! $latest) {
            return Stat::make('Current Weight', 'No data')
                ->description('Log your first weigh-in')
                ->icon('heroicon-o-scale')
                ->color('gray');
        }

        $chartData = $this->getChartData();

        return Stat::make('Current Weight', $latest->formatted_weight)
            ->description($latest->recorded_at->diffForHumans())
            ->descriptionIcon('heroicon-o-calendar')
            ->icon('heroicon-o-scale')
            ->chart($chartData)
            ->color('primary');
    }

    protected function getWeightChangeStat(): Stat
    {
        $latest = BodyWeight::query()
            ->orderByDesc('recorded_at')
            ->first();

        $thirtyDaysAgo = BodyWeight::query()
            ->where('recorded_at', '<=', now()->subDays(30))
            ->orderByDesc('recorded_at')
            ->first();

        if (! $latest) {
            return Stat::make('30 Day Change', 'No data')
                ->icon('heroicon-o-arrow-path')
                ->color('gray');
        }

        if (! $thirtyDaysAgo) {
            return Stat::make('30 Day Change', 'Not enough data')
                ->description('Need data from 30+ days ago')
                ->icon('heroicon-o-arrow-path')
                ->color('gray');
        }

        $latestPounds = $latest->total_pounds;
        $previousPounds = $thirtyDaysAgo->total_pounds;
        $changePounds = $latestPounds - $previousPounds;

        // Convert to stones and pounds for display
        $changeStones = floor(abs($changePounds) / 14);
        $remainingPounds = round(abs($changePounds) % 14, 1);

        if ($changeStones > 0) {
            $changeFormatted = "{$changeStones}st {$remainingPounds}lbs";
        } else {
            $changeFormatted = number_format(abs($changePounds), 1).'lbs';
        }

        if ($changePounds > 0) {
            return Stat::make('30 Day Change', "+{$changeFormatted}")
                ->description('Weight gain')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('danger');
        } elseif ($changePounds < 0) {
            return Stat::make('30 Day Change', "-{$changeFormatted}")
                ->description('Weight loss')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->icon('heroicon-o-arrow-trending-down')
                ->color('success');
        }

        return Stat::make('30 Day Change', 'No change')
            ->description('Maintaining weight')
            ->icon('heroicon-o-minus')
            ->color('gray');
    }

    /**
     * @return array<int>
     */
    protected function getChartData(): array
    {
        $weights = BodyWeight::query()
            ->orderBy('recorded_at')
            ->limit(14)
            ->get();

        if ($weights->isEmpty()) {
            return [0];
        }

        return $weights
            ->map(fn (BodyWeight $weight) => (int) $weight->total_pounds)
            ->toArray();
    }
}
