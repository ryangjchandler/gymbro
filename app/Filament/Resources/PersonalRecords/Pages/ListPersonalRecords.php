<?php

namespace App\Filament\Resources\PersonalRecords\Pages;

use App\Filament\Resources\PersonalRecords\PersonalRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPersonalRecords extends ListRecords
{
    protected static string $resource = PersonalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
