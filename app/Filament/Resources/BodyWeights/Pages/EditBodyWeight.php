<?php

namespace App\Filament\Resources\BodyWeights\Pages;

use App\Filament\Resources\BodyWeights\BodyWeightResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBodyWeight extends EditRecord
{
    protected static string $resource = BodyWeightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
