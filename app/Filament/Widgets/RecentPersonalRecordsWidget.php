<?php

namespace App\Filament\Widgets;

use App\Models\PersonalRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentPersonalRecordsWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Personal Records';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    protected ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => PersonalRecord::query()
                    ->with('exercise')
                    ->orderByDesc('achieved_at')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('exercise.name')
                    ->label('Exercise')
                    ->icon('heroicon-o-trophy')
                    ->iconColor('warning'),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('formattedValue')
                    ->label('Value'),
                TextColumn::make('achieved_at')
                    ->label('Date')
                    ->date()
                    ->since(),
            ]);
    }
}
