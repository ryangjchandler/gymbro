<?php

namespace App\Filament\Resources\BodyWeights\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BodyWeightsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recorded_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('formattedWeight')
                    ->label('Weight')
                    ->searchable(false),
                TextColumn::make('totalKilograms')
                    ->label('kg')
                    ->formatStateUsing(fn ($state): string => "{$state} kg")
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw('(stones * 14 + pounds) '.$direction);
                    }),
                TextColumn::make('notes')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('recorded_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
