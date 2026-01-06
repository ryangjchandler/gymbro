<?php

namespace App\Filament\Resources\BodyWeights\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BodyWeightForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Weight')
                    ->schema([
                        DatePicker::make('recorded_at')
                            ->label('Date')
                            ->required()
                            ->default(today()),
                        TextInput::make('stones')
                            ->label('Stones')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(50)
                            ->required()
                            ->suffix('st'),
                        TextInput::make('pounds')
                            ->label('Pounds')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(13.9)
                            ->step(0.1)
                            ->required()
                            ->suffix('lbs'),
                    ])
                    ->columns(3),
                Section::make('Photo & Notes')
                    ->schema([
                        FileUpload::make('photo_path')
                            ->label('Progress Photo')
                            ->image()
                            ->directory('body-weights')
                            ->visibility('private'),
                        Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->collapsed(),
            ]);
    }
}
