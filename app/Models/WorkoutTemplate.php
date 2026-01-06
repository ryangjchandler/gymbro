<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutTemplate extends Model
{
    use HasFactory;

    public function workoutTemplateExercises(): HasMany
    {
        return $this->hasMany(WorkoutTemplateExercise::class)->orderBy('order');
    }

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'workout_template_exercises')
            ->withPivot(['order', 'target_sets', 'target_reps', 'target_duration_seconds', 'rest_seconds', 'notes'])
            ->orderByPivot('order');
    }

    public function weeklySchedules(): HasMany
    {
        return $this->hasMany(WeeklySchedule::class);
    }

    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class);
    }
}
