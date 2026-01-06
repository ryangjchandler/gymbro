<?php

namespace App\Filament\Resources\Workouts\Pages;

use App\Filament\Resources\Workouts\WorkoutResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkouts extends ListRecords
{
    protected static string $resource = WorkoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
