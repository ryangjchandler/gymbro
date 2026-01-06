<?php

namespace App\Filament\Widgets;

use App\Models\Workout;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class WorkoutStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            $this->getWorkoutsThisWeekStat(),
            $this->getWorkoutsThisMonthStat(),
            $this->getStreakStat(),
        ];
    }

    protected function getWorkoutsThisWeekStat(): Stat
    {
        $count = Workout::query()
            ->completed()
            ->whereBetween('completed_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])
            ->count();

        $lastWeekCount = Workout::query()
            ->completed()
            ->whereBetween('completed_at', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek(),
            ])
            ->count();

        $stat = Stat::make('This Week', $count)
            ->icon('heroicon-o-calendar');

        if ($lastWeekCount > 0) {
            $change = $count - $lastWeekCount;

            if ($change > 0) {
                $stat->description("+{$change} vs last week")
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('success');
            } elseif ($change < 0) {
                $stat->description("{$change} vs last week")
                    ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color('danger');
            } else {
                $stat->description('Same as last week')
                    ->color('gray');
            }
        }

        return $stat;
    }

    protected function getWorkoutsThisMonthStat(): Stat
    {
        $count = Workout::query()
            ->completed()
            ->whereBetween('completed_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->count();

        $lastMonthCount = Workout::query()
            ->completed()
            ->whereBetween('completed_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->count();

        $stat = Stat::make('This Month', $count)
            ->icon('heroicon-o-calendar-days');

        if ($lastMonthCount > 0) {
            $change = $count - $lastMonthCount;

            if ($change > 0) {
                $stat->description("+{$change} vs last month")
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('success');
            } elseif ($change < 0) {
                $stat->description("{$change} vs last month")
                    ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color('danger');
            } else {
                $stat->description('Same as last month')
                    ->color('gray');
            }
        }

        return $stat;
    }

    protected function getStreakStat(): Stat
    {
        $streak = $this->calculateStreak();

        $stat = Stat::make('Current Streak', $streak === 1 ? '1 day' : "{$streak} days")
            ->icon('heroicon-o-fire');

        if ($streak >= 7) {
            $stat->description('On fire!')
                ->descriptionIcon('heroicon-m-fire')
                ->color('success');
        } elseif ($streak >= 3) {
            $stat->description('Keep it up!')
                ->color('warning');
        } elseif ($streak > 0) {
            $stat->description('Building momentum')
                ->color('info');
        } else {
            $stat->description('Start your streak today')
                ->color('gray');
        }

        return $stat;
    }

    protected function calculateStreak(): int
    {
        $workouts = Workout::query()
            ->completed()
            ->orderByDesc('completed_at')
            ->pluck('completed_at')
            ->map(fn (Carbon $date) => $date->toDateString())
            ->unique()
            ->values();

        if ($workouts->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Check if the streak is still active (worked out today or yesterday)
        $firstWorkoutDate = $workouts->first();

        if ($firstWorkoutDate !== $today && $firstWorkoutDate !== $yesterday) {
            return 0;
        }

        // Count consecutive days
        $checkDate = Carbon::parse($firstWorkoutDate);

        foreach ($workouts as $workoutDate) {
            if ($workoutDate === $checkDate->toDateString()) {
                $streak++;
                $checkDate = $checkDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
