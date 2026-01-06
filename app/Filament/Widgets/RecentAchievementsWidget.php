<?php

namespace App\Filament\Widgets;

use App\Models\UserAchievement;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentAchievementsWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Achievements';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    protected ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => UserAchievement::query()
                    ->orderByDesc('achieved_at')
                    ->limit(5)
            )
            ->paginated(false)
            ->emptyStateHeading('No achievements yet')
            ->emptyStateDescription('Complete workouts to earn your first achievement!')
            ->emptyStateIcon(Heroicon::Trophy)
            ->columns([
                TextColumn::make('achievement')
                    ->label('Achievement')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->description(fn ($state) => $state->getDescription())
                    ->icon(Heroicon::Trophy)
                    ->iconColor('warning'),
                TextColumn::make('tier')
                    ->label('Tier')
                    ->getStateUsing(fn (UserAchievement $record) => $record->achievement->getTier()->getLabel())
                    ->badge()
                    ->color(fn (UserAchievement $record) => match ($record->achievement->getTier()->value) {
                        'bronze' => 'warning',
                        'silver' => 'gray',
                        'gold' => 'warning',
                        'platinum' => 'info',
                        'ruby' => 'danger',
                        'diamond' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('achieved_at')
                    ->label('Achieved')
                    ->dateTime()
                    ->since(),
            ]);
    }
}
