<?php

namespace App\Filament\Widgets;

use App\Enums\ExerciseType;
use App\Models\Exercise;
use App\Models\WorkoutSet;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Carbon;

class ExerciseProgressChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Exercise Progress';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    protected ?string $pollingInterval = null;

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('exercise_id')
                ->label('Exercise')
                ->options(
                    Exercise::query()
                        ->where('type', ExerciseType::Strength)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->preload()
                ->placeholder('Select an exercise'),
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
        $exerciseId = $this->filters['exercise_id'] ?? null;
        $period = $this->filters['period'] ?? '90';

        if (! $exerciseId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $exercise = Exercise::find($exerciseId);

        if (! $exercise) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $query = WorkoutSet::query()
            ->where('exercise_id', $exerciseId)
            ->where('is_warmup', false)
            ->whereNotNull('weight_kg')
            ->whereHas('workout', fn ($q) => $q->completed())
            ->with('workout');

        if ($period !== 'all') {
            $query->whereHas('workout', function ($q) use ($period) {
                $q->where('completed_at', '>=', now()->subDays((int) $period));
            });
        }

        $sets = $query->get();

        if ($sets->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Group by date and get max weight per day
        $groupedByDate = $sets->groupBy(function (WorkoutSet $set) {
            return $set->workout->completed_at->toDateString();
        });

        $data = $groupedByDate
            ->map(fn ($daySets) => (float) $daySets->max('weight_kg'))
            ->sortKeys();

        $labels = $data->keys()->map(fn ($date) => Carbon::parse($date)->format('M j'))->toArray();
        $weights = $data->values()->toArray();

        $datasets = [
            [
                'label' => 'Max Weight (kg)',
                'data' => $weights,
                'borderColor' => '#f59e0b',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                'fill' => true,
                'tension' => 0.3,
            ],
        ];

        // Add estimated 1RM reference line if available
        $estimated1rm = $exercise->getEstimated1rm();

        if ($estimated1rm && count($labels) > 0) {
            $estimated1rmValue = $estimated1rm['estimated_1rm'];

            $datasets[] = [
                'label' => 'Est. 1RM ('.$estimated1rmValue.'kg)',
                'data' => array_fill(0, count($labels), $estimated1rmValue),
                'borderColor' => '#10b981',
                'borderDash' => [5, 5],
                'borderWidth' => 2,
                'pointRadius' => 0,
                'fill' => false,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
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
                            callback: (value) => value + ' kg',
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => context.dataset.label.includes('1RM') 
                                ? context.dataset.label 
                                : context.parsed.y + ' kg',
                        },
                    },
                },
            }
        JS);
    }
}
