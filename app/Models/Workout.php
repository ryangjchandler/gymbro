<?php

namespace App\Models;

use App\Enums\WorkoutStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workout extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'status' => WorkoutStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function workoutTemplate(): BelongsTo
    {
        return $this->belongsTo(WorkoutTemplate::class);
    }

    public function weeklySchedule(): BelongsTo
    {
        return $this->belongsTo(WeeklySchedule::class);
    }

    public function workoutSets(): HasMany
    {
        return $this->hasMany(WorkoutSet::class);
    }

    public function cardioLogs(): HasMany
    {
        return $this->hasMany(CardioLog::class);
    }

    public function scopeForDate(Builder $query, $date): Builder
    {
        return $query->whereDate('scheduled_date', $date);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', WorkoutStatus::Completed);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', WorkoutStatus::InProgress);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', WorkoutStatus::Pending);
    }

    protected function totalVolume(): Attribute
    {
        return Attribute::get(fn () => $this->workoutSets
            ->reject(fn (WorkoutSet $set) => $set->is_warmup)
            ->sum(fn (WorkoutSet $set) => ($set->weight_kg ?? 0) * ($set->reps ?? 0)));
    }

    protected function durationInMinutes(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->started_at || ! $this->completed_at) {
                return null;
            }

            return (int) $this->started_at->diffInMinutes($this->completed_at);
        });
    }
}
