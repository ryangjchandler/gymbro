<?php

namespace App\Models;

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use Illuminate\Database\Eloquent\Builder;
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
            'is_pinned' => 'boolean',
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

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeStrength(Builder $query): Builder
    {
        return $query->where('type', ExerciseType::Strength);
    }

    /**
     * Calculate estimated 1RM using the Epley formula.
     */
    public static function calculateEpley1rm(float $weight, int $reps): float
    {
        if ($reps === 1) {
            return $weight;
        }

        return round($weight * (1 + $reps / 30), 1);
    }

    /**
     * Get the estimated 1RM based on the best qualifying workout set.
     *
     * @return array{weight_kg: float, reps: int, estimated_1rm: float}|null
     */
    public function getEstimated1rm(): ?array
    {
        if (! $this->isStrength()) {
            return null;
        }

        $bestSet = $this->workoutSets()
            ->where('is_warmup', false)
            ->where('reps', '>=', 1)
            ->where('reps', '<=', 10)
            ->whereNotNull('weight_kg')
            ->where('weight_kg', '>', 0)
            ->get()
            ->map(fn (WorkoutSet $set) => [
                'weight_kg' => (float) $set->weight_kg,
                'reps' => $set->reps,
                'estimated_1rm' => self::calculateEpley1rm((float) $set->weight_kg, $set->reps),
            ])
            ->sortByDesc('estimated_1rm')
            ->first();

        return $bestSet;
    }
}
