<?php

namespace App\Filament\Resources\PersonalRecords\Pages;

use App\Filament\Resources\PersonalRecords\PersonalRecordResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPersonalRecord extends ViewRecord
{
    protected static string $resource = PersonalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
