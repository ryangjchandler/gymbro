<?php

namespace App\Models;

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'muscle_group' => MuscleGroup::class,
            'type' => ExerciseType::class,
        ];
    }

    public function workoutSets(): HasMany
    {
        return $this->hasMany(WorkoutSet::class);
    }

    public function cardioLogs(): HasMany
    {
        return $this->hasMany(CardioLog::class);
    }

    public function personalRecords(): HasMany
    {
        return $this->hasMany(PersonalRecord::class);
    }

    public function workoutTemplateExercises(): HasMany
    {
        return $this->hasMany(WorkoutTemplateExercise::class);
    }

    public function isStrength(): bool
    {
        return $this->type === ExerciseType::Strength;
    }

    public function isCardio(): bool
    {
        return $this->type === ExerciseType::Cardio;
    }

    public function isTimed(): bool
    {
        return $this->type === ExerciseType::Timed;
    }
}
