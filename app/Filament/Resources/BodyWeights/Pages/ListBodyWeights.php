<?php

namespace App\Filament\Resources\BodyWeights\Pages;

use App\Filament\Resources\BodyWeights\BodyWeightResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBodyWeights extends ListRecords
{
    protected static string $resource = BodyWeightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
