<?php

namespace App\Filament\Resources\Workouts\Pages;

use App\Filament\Resources\Workouts\WorkoutResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkout extends CreateRecord
{
    protected static string $resource = WorkoutResource::class;
}
