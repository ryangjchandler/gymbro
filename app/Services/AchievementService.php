<?php

namespace App\Services;

use App\Enums\Achievement;
use App\Enums\AchievementCategory;
use App\Models\BodyWeight;
use App\Models\Exercise;
use App\Models\PersonalRecord;
use App\Models\UserAchievement;
use App\Models\Workout;
use App\Models\WorkoutSet;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AchievementService
{
    /**
     * Check if an achievement's criteria has been met.
     */
    public function check(Achievement $achievement): bool
    {
        return $this->getCurrentValue($achievement) >= $achievement->getThreshold();
    }

    /**
     * Get the current progress value for an achievement.
     */
    public function getCurrentValue(Achievement $achievement): int|float
    {
        return match ($achievement) {
            // Consistency - Workout counts
            Achievement::FirstWorkout,
            Achievement::Workouts10,
            Achievement::Workouts50,
            Achievement::Workouts100,
            Achievement::Workouts500 => Workout::query()->completed()->count(),

            // Consistency - Streaks
            Achievement::Streak3Days,
            Achievement::Streak7Days,
            Achievement::Streak14Days,
            Achievement::Streak30Days => $this->getCurrentStreak(),

            // Strength - PR counts
            Achievement::FirstPR,
            Achievement::PRs10,
            Achievement::PRs50 => PersonalRecord::query()->count(),

            // Strength - Estimated 1RM
            Achievement::Estimated1RM100kg,
            Achievement::Estimated1RM150kg,
            Achievement::Estimated1RM200kg => $this->getMax1rm(),

            // Volume - Lifetime
            Achievement::Volume10k,
            Achievement::Volume100k,
            Achievement::Volume500k,
            Achievement::Volume1M => $this->getLifetimeVolume(),

            // Volume - Weekly
            Achievement::WeeklyVolume10k => $this->getCurrentWeekVolume(),

            // Variety
            Achievement::Exercises5,
            Achievement::Exercises20 => $this->getUniqueExerciseCount(),
            Achievement::AllMuscleGroups => $this->getMuscleGroupsThisWeek(),

            // Body weight
            Achievement::FirstWeighIn => BodyWeight::query()->count(),
            Achievement::WeighIn7Days,
            Achievement::WeighIn30Days => $this->getBodyWeightStreak(),
        };
    }

    /**
     * Get the progress for an achievement.
     *
     * @return array{current: int|float, threshold: int, percentage: int}
     */
    public function getProgress(Achievement $achievement): array
    {
        $current = $this->getCurrentValue($achievement);
        $threshold = $achievement->getThreshold();
        $percentage = min(100, (int) round(($current / $threshold) * 100));

        return [
            'current' => $current,
            'threshold' => $threshold,
            'percentage' => $percentage,
        ];
    }

    /**
     * Unlock an achievement if it hasn't been unlocked yet.
     */
    public function unlock(Achievement $achievement): ?UserAchievement
    {
        if ($this->isUnlocked($achievement)) {
            return null;
        }

        return UserAchievement::query()->create([
            'achievement' => $achievement,
            'achieved_at' => now(),
        ]);
    }

    /**
     * Check if an achievement has been unlocked.
     */
    public function isUnlocked(Achievement $achievement): bool
    {
        return UserAchievement::query()
            ->where('achievement', $achievement->value)
            ->exists();
    }

    /**
     * Check and unlock all eligible achievements.
     *
     * @return Collection<int, UserAchievement>
     */
    public function checkAndUnlockAll(): Collection
    {
        $unlocked = collect();

        foreach (Achievement::cases() as $achievement) {
            if (! $this->isUnlocked($achievement) && $this->check($achievement)) {
                $userAchievement = $this->unlock($achievement);
                if ($userAchievement) {
                    $unlocked->push($userAchievement);
                }
            }
        }

        return $unlocked;
    }

    /**
     * Get all unlocked achievements.
     *
     * @return Collection<int, UserAchievement>
     */
    public function getUnlocked(): Collection
    {
        return UserAchievement::query()
            ->orderByDesc('achieved_at')
            ->get();
    }

    /**
     * Get all locked achievements with their progress.
     *
     * @return Collection<int, array{achievement: Achievement, progress: array{current: int|float, threshold: int, percentage: int}}>
     */
    public function getLockedWithProgress(): Collection
    {
        $unlockedValues = UserAchievement::query()
            ->pluck('achievement')
            ->map(fn ($a) => $a->value)
            ->toArray();

        return collect(Achievement::cases())
            ->reject(fn (Achievement $a) => in_array($a->value, $unlockedValues))
            ->map(fn (Achievement $a) => [
                'achievement' => $a,
                'progress' => $this->getProgress($a),
            ])
            ->values();
    }

    /**
     * Get achievements that are almost achieved (>= 75% progress).
     *
     * @return Collection<int, array{achievement: Achievement, progress: array{current: int|float, threshold: int, percentage: int}}>
     */
    public function getAlmostAchieved(int $limit = 5): Collection
    {
        return $this->getLockedWithProgress()
            ->filter(fn (array $item) => $item['progress']['percentage'] >= 75)
            ->sortByDesc(fn (array $item) => $item['progress']['percentage'])
            ->take($limit)
            ->values();
    }

    /**
     * Get all achievements for a category with their status.
     *
     * @return Collection<int, array{achievement: Achievement, unlocked: bool, achieved_at: ?Carbon, progress: array{current: int|float, threshold: int, percentage: int}}>
     */
    public function getForCategory(AchievementCategory $category): Collection
    {
        $unlocked = UserAchievement::query()
            ->get()
            ->keyBy(fn (UserAchievement $ua) => $ua->achievement->value);

        return collect(Achievement::forCategory($category))
            ->map(fn (Achievement $a) => [
                'achievement' => $a,
                'unlocked' => $unlocked->has($a->value),
                'achieved_at' => $unlocked->get($a->value)?->achieved_at,
                'progress' => $this->getProgress($a),
            ])
            ->values();
    }

    /**
     * Calculate the current workout streak (consecutive days with completed workouts).
     */
    protected function getCurrentStreak(): int
    {
        $dates = Workout::query()
            ->completed()
            ->orderByDesc('scheduled_date')
            ->pluck('scheduled_date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->unique()
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $expectedDate = Carbon::today();

        // Allow for today or yesterday to be the start of the streak
        $firstDate = Carbon::parse($dates->first());
        if ($firstDate->lt($expectedDate->copy()->subDay())) {
            return 0;
        }

        if ($firstDate->isSameDay($expectedDate->copy()->subDay())) {
            $expectedDate = $expectedDate->subDay();
        }

        foreach ($dates as $date) {
            if (Carbon::parse($date)->isSameDay($expectedDate)) {
                $streak++;
                $expectedDate = $expectedDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get the highest estimated 1RM across all exercises.
     */
    protected function getMax1rm(): float
    {
        $max = 0;

        Exercise::query()
            ->strength()
            ->get()
            ->each(function (Exercise $exercise) use (&$max) {
                $result = $exercise->getEstimated1rm();
                if ($result && $result['estimated_1rm'] > $max) {
                    $max = $result['estimated_1rm'];
                }
            });

        return $max;
    }

    /**
     * Get the lifetime total volume (kg lifted).
     */
    protected function getLifetimeVolume(): float
    {
        return WorkoutSet::query()
            ->where('is_warmup', false)
            ->get()
            ->sum(fn (WorkoutSet $set) => ($set->weight_kg ?? 0) * ($set->reps ?? 0));
    }

    /**
     * Get the current week's total volume.
     */
    protected function getCurrentWeekVolume(): float
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return WorkoutSet::query()
            ->where('is_warmup', false)
            ->whereHas('workout', fn ($q) => $q
                ->completed()
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek]))
            ->get()
            ->sum(fn (WorkoutSet $set) => ($set->weight_kg ?? 0) * ($set->reps ?? 0));
    }

    /**
     * Get the count of unique exercises performed.
     */
    protected function getUniqueExerciseCount(): int
    {
        return WorkoutSet::query()
            ->distinct('exercise_id')
            ->count('exercise_id');
    }

    /**
     * Get the count of unique muscle groups hit this week.
     */
    protected function getMuscleGroupsThisWeek(): int
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return WorkoutSet::query()
            ->whereHas('workout', fn ($q) => $q
                ->completed()
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek]))
            ->with('exercise')
            ->get()
            ->pluck('exercise.muscle_group')
            ->unique()
            ->filter()
            ->count();
    }

    /**
     * Get the current body weight tracking streak.
     */
    protected function getBodyWeightStreak(): int
    {
        $dates = BodyWeight::query()
            ->orderByDesc('recorded_at')
            ->pluck('recorded_at')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->unique()
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $expectedDate = Carbon::today();

        // Allow for today or yesterday to be the start of the streak
        $firstDate = Carbon::parse($dates->first());
        if ($firstDate->lt($expectedDate->copy()->subDay())) {
            return 0;
        }

        if ($firstDate->isSameDay($expectedDate->copy()->subDay())) {
            $expectedDate = $expectedDate->subDay();
        }

        foreach ($dates as $date) {
            if (Carbon::parse($date)->isSameDay($expectedDate)) {
                $streak++;
                $expectedDate = $expectedDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
