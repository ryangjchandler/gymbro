<?php

namespace App\Filament\Resources\WorkoutTemplates\Pages;

use App\Filament\Resources\WorkoutTemplates\WorkoutTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkoutTemplate extends EditRecord
{
    protected static string $resource = WorkoutTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
