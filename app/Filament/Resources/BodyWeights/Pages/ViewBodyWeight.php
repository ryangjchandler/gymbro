<?php

namespace App\Filament\Resources\BodyWeights\Pages;

use App\Filament\Resources\BodyWeights\BodyWeightResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBodyWeight extends ViewRecord
{
    protected static string $resource = BodyWeightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
