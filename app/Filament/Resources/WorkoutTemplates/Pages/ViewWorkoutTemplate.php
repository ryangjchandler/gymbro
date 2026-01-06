<?php

namespace App\Filament\Resources\WorkoutTemplates\Pages;

use App\Filament\Resources\WorkoutTemplates\WorkoutTemplateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkoutTemplate extends ViewRecord
{
    protected static string $resource = WorkoutTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
