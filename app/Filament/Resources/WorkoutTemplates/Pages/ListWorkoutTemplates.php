<?php

namespace App\Filament\Resources\WorkoutTemplates\Pages;

use App\Filament\Resources\WorkoutTemplates\WorkoutTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkoutTemplates extends ListRecords
{
    protected static string $resource = WorkoutTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
