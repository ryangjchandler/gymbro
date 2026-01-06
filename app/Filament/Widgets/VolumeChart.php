<?php

namespace App\Filament\Widgets;

use App\Models\Workout;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Carbon;

class VolumeChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Training Volume Over Time';

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    protected ?string $pollingInterval = null;

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('period')
                ->label('Period')
                ->options([
                    '30' => 'Last 30 days',
                    '90' => 'Last 90 days',
                    '180' => 'Last 6 months',
                    '365' => 'Last year',
                ])
                ->default('90'),
            Select::make('grouping')
                ->label('Group by')
                ->options([
                    'day' => 'Day',
                    'week' => 'Week',
                    'month' => 'Month',
                ])
                ->default('week'),
        ]);
    }

    protected function getData(): array
    {
        $period = $this->filters['period'] ?? '90';
        $grouping = $this->filters['grouping'] ?? 'week';

        $startDate = now()->subDays((int) $period);

        $workouts = Workout::query()
            ->completed()
            ->where('completed_at', '>=', $startDate)
            ->with('workoutSets')
            ->get();

        if ($workouts->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Calculate volume for each workout
        $volumeData = $workouts->map(function (Workout $workout) {
            return [
                'date' => $workout->completed_at,
                'volume' => $workout->total_volume,
            ];
        });

        // Group by the selected period
        $grouped = $volumeData->groupBy(function ($item) use ($grouping) {
            return match ($grouping) {
                'day' => $item['date']->toDateString(),
                'week' => $item['date']->startOfWeek()->toDateString(),
                'month' => $item['date']->startOfMonth()->toDateString(),
            };
        });

        // Sum volume per group
        $data = $grouped
            ->map(fn ($items) => $items->sum('volume'))
            ->sortKeys();

        // Format labels based on grouping
        $labels = $data->keys()->map(function ($date) use ($grouping) {
            $carbon = Carbon::parse($date);

            return match ($grouping) {
                'day' => $carbon->format('M j'),
                'week' => 'W'.$carbon->weekOfYear,
                'month' => $carbon->format('M Y'),
            };
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Volume (kg)',
                    'data' => $data->values()->toArray(),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.5)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => {
                                if (value >= 1000) {
                                    return (value / 1000).toFixed(1) + 'k kg';
                                }
                                return value + ' kg';
                            },
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const value = context.parsed.y;
                                if (value >= 1000) {
                                    return (value / 1000).toFixed(1) + 'k kg total volume';
                                }
                                return value + ' kg total volume';
                            },
                        },
                    },
                },
            }
        JS);
    }
}
