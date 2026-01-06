<?php

namespace App\Filament\Resources\PersonalRecords\Tables;

use App\Enums\PersonalRecordType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PersonalRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('exercise.name')
                    ->label('Exercise')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('formattedValue')
                    ->label('Value')
                    ->searchable(false),
                TextColumn::make('achieved_at')
                    ->label('Date Achieved')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('achieved_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->options(PersonalRecordType::class),
                SelectFilter::make('exercise_id')
                    ->relationship('exercise', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Exercise'),
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
