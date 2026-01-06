<?php

namespace App\Filament\Widgets;

use App\Models\Exercise;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class Estimated1rmWidget extends TableWidget
{
    protected static ?string $heading = 'Estimated 1RM';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    protected ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Exercise::query()
                    ->pinned()
                    ->strength()
                    ->orderBy('name')
            )
            ->paginated(false)
            ->emptyStateHeading('No pinned exercises')
            ->emptyStateDescription('Pin strength exercises to see their estimated 1RM here.')
            ->emptyStateIcon('heroicon-o-star')
            ->columns([
                TextColumn::make('name')
                    ->label('Exercise')
                    ->icon('heroicon-o-star')
                    ->iconColor('warning'),
                TextColumn::make('best_set')
                    ->label('Best Set')
                    ->getStateUsing(function (Exercise $record): string {
                        $data = $record->getEstimated1rm();

                        if (! $data) {
                            return '-';
                        }

                        return number_format($data['weight_kg'], 1).'kg × '.$data['reps'];
                    }),
                TextColumn::make('estimated_1rm')
                    ->label('Est. 1RM')
                    ->getStateUsing(function (Exercise $record): string {
                        $data = $record->getEstimated1rm();

                        if (! $data) {
                            return '-';
                        }

                        return number_format($data['estimated_1rm'], 1).'kg';
                    })
                    ->badge()
                    ->color('success'),
            ]);
    }
}
