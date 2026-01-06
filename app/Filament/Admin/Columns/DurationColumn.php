<?php

namespace App\Filament\Admin\Columns;

use Filament\Tables\Columns\TextColumn;

class DurationColumn
{
    public static function make(string $name = 'duration_seconds'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Duration')
            ->formatStateUsing(function (?int $state): string {
                if ($state === null) {
                    return '-';
                }

                $minutes = intdiv($state, 60);
                $seconds = $state % 60;

                if ($minutes === 0) {
                    return "{$seconds}s";
                }

                return $seconds > 0
                    ? "{$minutes}m {$seconds}s"
                    : "{$minutes}m";
            })
            ->sortable();
    }
}
