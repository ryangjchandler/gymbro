<?php

namespace App\Filament\Widgets;

use App\Models\BodyWeight;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class BodyWeightChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Body Weight Over Time';

    protected static ?int $sort = 6;

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
                    'all' => 'All time',
                ])
                ->default('90'),
        ]);
    }

    protected function getData(): array
    {
        $period = $this->filters['period'] ?? '90';

        $query = BodyWeight::query()->orderBy('recorded_at');

        if ($period !== 'all') {
            $query->where('recorded_at', '>=', now()->subDays((int) $period));
        }

        $weights = $query->get();

        if ($weights->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Weight (lbs)',
                    'data' => $weights->map(fn (BodyWeight $w) => (float) $w->total_pounds)->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $weights->map(fn (BodyWeight $w) => $w->recorded_at->format('M j'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: (value) => {
                                const stones = Math.floor(value / 14);
                                const pounds = Math.round(value % 14);
                                return stones + 'st ' + pounds + 'lbs';
                            },
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const value = context.parsed.y;
                                const stones = Math.floor(value / 14);
                                const pounds = Math.round((value % 14) * 10) / 10;
                                return stones + 'st ' + pounds + 'lbs';
                            },
                        },
                    },
                },
            }
        JS);
    }
}
