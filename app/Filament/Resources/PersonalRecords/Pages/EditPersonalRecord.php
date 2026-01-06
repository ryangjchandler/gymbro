<?php

namespace App\Filament\Resources\PersonalRecords\Pages;

use App\Filament\Resources\PersonalRecords\PersonalRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPersonalRecord extends EditRecord
{
    protected static string $resource = PersonalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
