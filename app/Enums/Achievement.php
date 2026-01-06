<?php

namespace App\Enums;

enum Achievement: string
{
    // Consistency achievements
    case FirstWorkout = 'first_workout';
    case Streak3Days = 'streak_3_days';
    case Streak7Days = 'streak_7_days';
    case Workouts10 = 'workouts_10';
    case Streak14Days = 'streak_14_days';
    case Workouts50 = 'workouts_50';
    case Streak30Days = 'streak_30_days';
    case Workouts100 = 'workouts_100';
    case Workouts500 = 'workouts_500';

    // Strength achievements
    case FirstPR = 'first_pr';
    case PRs10 = 'prs_10';
    case Estimated1RM100kg = 'estimated_1rm_100kg';
    case PRs50 = 'prs_50';
    case Estimated1RM150kg = 'estimated_1rm_150kg';
    case Estimated1RM200kg = 'estimated_1rm_200kg';

    // Volume achievements
    case Volume10k = 'volume_10k';
    case Volume100k = 'volume_100k';
    case WeeklyVolume10k = 'weekly_volume_10k';
    case Volume500k = 'volume_500k';
    case Volume1M = 'volume_1m';

    // Variety achievements
    case Exercises5 = 'exercises_5';
    case Exercises20 = 'exercises_20';
    case AllMuscleGroups = 'all_muscle_groups';

    // Body weight achievements
    case FirstWeighIn = 'first_weigh_in';
    case WeighIn7Days = 'weigh_in_7_days';
    case WeighIn30Days = 'weigh_in_30_days';

    public function getLabel(): string
    {
        return match ($this) {
            // Consistency
            self::FirstWorkout => 'First Steps',
            self::Streak3Days => 'Getting Started',
            self::Streak7Days => 'Week Warrior',
            self::Workouts10 => 'Double Digits',
            self::Streak14Days => 'Two Week Streak',
            self::Workouts50 => 'Fifty Strong',
            self::Streak30Days => 'Monthly Master',
            self::Workouts100 => 'Century Club',
            self::Workouts500 => 'Iron Veteran',

            // Strength
            self::FirstPR => 'Record Breaker',
            self::PRs10 => 'PR Hunter',
            self::Estimated1RM100kg => 'Century Lift',
            self::PRs50 => 'PR Collector',
            self::Estimated1RM150kg => 'Heavy Hitter',
            self::Estimated1RM200kg => 'Elite Strength',

            // Volume
            self::Volume10k => 'Ten Ton',
            self::Volume100k => 'Hundred Ton',
            self::WeeklyVolume10k => 'Week of Steel',
            self::Volume500k => 'Half Million',
            self::Volume1M => 'Million Pound Club',

            // Variety
            self::Exercises5 => 'Variety Pack',
            self::Exercises20 => 'Exercise Explorer',
            self::AllMuscleGroups => 'Full Body Focus',

            // Body weight
            self::FirstWeighIn => 'Scale Starter',
            self::WeighIn7Days => 'Week Tracker',
            self::WeighIn30Days => 'Monthly Monitor',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            // Consistency
            self::FirstWorkout => 'Complete your first workout',
            self::Streak3Days => 'Work out 3 days in a row',
            self::Streak7Days => 'Work out 7 days in a row',
            self::Workouts10 => 'Complete 10 workouts',
            self::Streak14Days => 'Work out 14 days in a row',
            self::Workouts50 => 'Complete 50 workouts',
            self::Streak30Days => 'Work out 30 days in a row',
            self::Workouts100 => 'Complete 100 workouts',
            self::Workouts500 => 'Complete 500 workouts',

            // Strength
            self::FirstPR => 'Set your first personal record',
            self::PRs10 => 'Set 10 personal records',
            self::Estimated1RM100kg => 'Reach a 100kg estimated 1RM',
            self::PRs50 => 'Set 50 personal records',
            self::Estimated1RM150kg => 'Reach a 150kg estimated 1RM',
            self::Estimated1RM200kg => 'Reach a 200kg estimated 1RM',

            // Volume
            self::Volume10k => 'Lift 10,000kg total',
            self::Volume100k => 'Lift 100,000kg total',
            self::WeeklyVolume10k => 'Lift 10,000kg in a single week',
            self::Volume500k => 'Lift 500,000kg total',
            self::Volume1M => 'Lift 1,000,000kg total',

            // Variety
            self::Exercises5 => 'Try 5 different exercises',
            self::Exercises20 => 'Try 20 different exercises',
            self::AllMuscleGroups => 'Hit all muscle groups in one week',

            // Body weight
            self::FirstWeighIn => 'Log your first body weight',
            self::WeighIn7Days => 'Log body weight 7 days in a row',
            self::WeighIn30Days => 'Log body weight 30 days in a row',
        };
    }

    public function getCategory(): AchievementCategory
    {
        return match ($this) {
            self::FirstWorkout,
            self::Streak3Days,
            self::Streak7Days,
            self::Workouts10,
            self::Streak14Days,
            self::Workouts50,
            self::Streak30Days,
            self::Workouts100,
            self::Workouts500 => AchievementCategory::Consistency,

            self::FirstPR,
            self::PRs10,
            self::Estimated1RM100kg,
            self::PRs50,
            self::Estimated1RM150kg,
            self::Estimated1RM200kg => AchievementCategory::Strength,

            self::Volume10k,
            self::Volume100k,
            self::WeeklyVolume10k,
            self::Volume500k,
            self::Volume1M => AchievementCategory::Volume,

            self::Exercises5,
            self::Exercises20,
            self::AllMuscleGroups => AchievementCategory::Variety,

            self::FirstWeighIn,
            self::WeighIn7Days,
            self::WeighIn30Days => AchievementCategory::BodyWeight,
        };
    }

    public function getTier(): AchievementTier
    {
        return match ($this) {
            // Bronze
            self::FirstWorkout,
            self::Streak3Days,
            self::FirstPR,
            self::Volume10k,
            self::Exercises5,
            self::FirstWeighIn => AchievementTier::Bronze,

            // Silver
            self::Streak7Days,
            self::Workouts10,
            self::PRs10,
            self::Estimated1RM100kg,
            self::Volume100k,
            self::WeeklyVolume10k,
            self::Exercises20,
            self::WeighIn7Days => AchievementTier::Silver,

            // Gold
            self::Streak14Days,
            self::Workouts50,
            self::PRs50,
            self::Estimated1RM150kg,
            self::Volume500k,
            self::AllMuscleGroups,
            self::WeighIn30Days => AchievementTier::Gold,

            // Platinum
            self::Streak30Days,
            self::Workouts100,
            self::Estimated1RM200kg,
            self::Volume1M => AchievementTier::Platinum,

            // Diamond
            self::Workouts500 => AchievementTier::Diamond,
        };
    }

    public function getThreshold(): int
    {
        return match ($this) {
            // Consistency
            self::FirstWorkout => 1,
            self::Streak3Days => 3,
            self::Streak7Days => 7,
            self::Workouts10 => 10,
            self::Streak14Days => 14,
            self::Workouts50 => 50,
            self::Streak30Days => 30,
            self::Workouts100 => 100,
            self::Workouts500 => 500,

            // Strength
            self::FirstPR => 1,
            self::PRs10 => 10,
            self::Estimated1RM100kg => 100,
            self::PRs50 => 50,
            self::Estimated1RM150kg => 150,
            self::Estimated1RM200kg => 200,

            // Volume
            self::Volume10k => 10_000,
            self::Volume100k => 100_000,
            self::WeeklyVolume10k => 10_000,
            self::Volume500k => 500_000,
            self::Volume1M => 1_000_000,

            // Variety
            self::Exercises5 => 5,
            self::Exercises20 => 20,
            self::AllMuscleGroups => 10,

            // Body weight
            self::FirstWeighIn => 1,
            self::WeighIn7Days => 7,
            self::WeighIn30Days => 30,
        };
    }

    /**
     * @return array<Achievement>
     */
    public static function forCategory(AchievementCategory $category): array
    {
        return array_filter(
            self::cases(),
            fn (Achievement $achievement) => $achievement->getCategory() === $category
        );
    }
}
