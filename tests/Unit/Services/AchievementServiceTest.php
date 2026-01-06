<?php

use App\Enums\Achievement;
use App\Enums\AchievementCategory;
use App\Enums\WorkoutStatus;
use App\Models\BodyWeight;
use App\Models\Exercise;
use App\Models\PersonalRecord;
use App\Models\UserAchievement;
use App\Models\Workout;
use App\Models\WorkoutSet;
use App\Services\AchievementService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = new AchievementService;
});

describe('workout count achievements', function () {
    it('unlocks FirstWorkout after completing one workout', function () {
        Workout::factory()->create(['status' => WorkoutStatus::Completed]);

        expect($this->service->check(Achievement::FirstWorkout))->toBeTrue();
    });

    it('does not unlock FirstWorkout without completed workouts', function () {
        Workout::factory()->create(['status' => WorkoutStatus::Pending]);

        expect($this->service->check(Achievement::FirstWorkout))->toBeFalse();
    });

    it('unlocks Workouts10 after completing 10 workouts', function () {
        Workout::factory()->count(10)->create(['status' => WorkoutStatus::Completed]);

        expect($this->service->check(Achievement::Workouts10))->toBeTrue();
    });
});

describe('workout streak achievements', function () {
    it('calculates current streak correctly', function () {
        Carbon::setTestNow('2026-01-06 12:00:00');

        // Create workouts for 3 consecutive days
        Workout::factory()->create([
            'status' => WorkoutStatus::Completed,
            'scheduled_date' => '2026-01-06',
        ]);
        Workout::factory()->create([
            'status' => WorkoutStatus::Completed,
            'scheduled_date' => '2026-01-05',
        ]);
        Workout::factory()->create([
            'status' => WorkoutStatus::Completed,
            'scheduled_date' => '2026-01-04',
        ]);

        expect($this->service->check(Achievement::Streak3Days))->toBeTrue();

        Carbon::setTestNow();
    });

    it('breaks streak on missed day', function () {
        Carbon::setTestNow('2026-01-06 12:00:00');

        // Create workouts with a gap
        Workout::factory()->create([
            'status' => WorkoutStatus::Completed,
            'scheduled_date' => '2026-01-06',
        ]);
        // Skip 2026-01-05
        Workout::factory()->create([
            'status' => WorkoutStatus::Completed,
            'scheduled_date' => '2026-01-04',
        ]);

        expect($this->service->check(Achievement::Streak3Days))->toBeFalse();

        Carbon::setTestNow();
    });

    it('allows streak to start from yesterday', function () {
        Carbon::setTestNow('2026-01-06 12:00:00');

        // Workout was yesterday, not today
        Workout::factory()->create([
            'status' => WorkoutStatus::Completed,
            'scheduled_date' => '2026-01-05',
        ]);
        Workout::factory()->create([
            'status' => WorkoutStatus::Completed,
            'scheduled_date' => '2026-01-04',
        ]);
        Workout::factory()->create([
            'status' => WorkoutStatus::Completed,
            'scheduled_date' => '2026-01-03',
        ]);

        expect($this->service->check(Achievement::Streak3Days))->toBeTrue();

        Carbon::setTestNow();
    });
});

describe('PR count achievements', function () {
    it('unlocks FirstPR after setting one personal record', function () {
        PersonalRecord::factory()->create();

        expect($this->service->check(Achievement::FirstPR))->toBeTrue();
    });

    it('unlocks PRs10 after setting 10 personal records', function () {
        PersonalRecord::factory()->count(10)->create();

        expect($this->service->check(Achievement::PRs10))->toBeTrue();
    });
});

describe('estimated 1rm achievements', function () {
    it('unlocks Estimated1RM100kg when reaching 100kg estimated 1rm', function () {
        $exercise = Exercise::factory()->strength()->create();
        // 90kg x 5 reps = ~105kg estimated 1RM (Epley formula)
        WorkoutSet::factory()->create([
            'exercise_id' => $exercise->id,
            'weight_kg' => 90,
            'reps' => 5,
            'is_warmup' => false,
        ]);

        expect($this->service->check(Achievement::Estimated1RM100kg))->toBeTrue();
    });

    it('does not unlock Estimated1RM100kg with warmup sets', function () {
        $exercise = Exercise::factory()->strength()->create();
        WorkoutSet::factory()->create([
            'exercise_id' => $exercise->id,
            'weight_kg' => 100,
            'reps' => 1,
            'is_warmup' => true,
        ]);

        expect($this->service->check(Achievement::Estimated1RM100kg))->toBeFalse();
    });
});

describe('volume achievements', function () {
    it('unlocks Volume10k after lifting 10,000kg total', function () {
        // 100kg x 10 reps x 10 sets = 10,000kg
        WorkoutSet::factory()->count(10)->create([
            'weight_kg' => 100,
            'reps' => 10,
            'is_warmup' => false,
        ]);

        expect($this->service->check(Achievement::Volume10k))->toBeTrue();
    });

    it('excludes warmup sets from volume calculation', function () {
        WorkoutSet::factory()->count(10)->create([
            'weight_kg' => 100,
            'reps' => 10,
            'is_warmup' => true,
        ]);

        expect($this->service->check(Achievement::Volume10k))->toBeFalse();
    });
});

describe('variety achievements', function () {
    it('unlocks Exercises5 after trying 5 different exercises', function () {
        $exercises = Exercise::factory()->count(5)->create();
        foreach ($exercises as $exercise) {
            WorkoutSet::factory()->create(['exercise_id' => $exercise->id]);
        }

        expect($this->service->check(Achievement::Exercises5))->toBeTrue();
    });

    it('counts unique exercises only', function () {
        $exercise = Exercise::factory()->create();
        WorkoutSet::factory()->count(10)->create(['exercise_id' => $exercise->id]);

        expect($this->service->getCurrentValue(Achievement::Exercises5))->toBe(1);
    });
});

describe('body weight achievements', function () {
    it('unlocks FirstWeighIn after logging first body weight', function () {
        BodyWeight::factory()->create();

        expect($this->service->check(Achievement::FirstWeighIn))->toBeTrue();
    });

    it('calculates body weight streak correctly', function () {
        Carbon::setTestNow('2026-01-06 12:00:00');

        BodyWeight::factory()->create(['recorded_at' => '2026-01-06']);
        BodyWeight::factory()->create(['recorded_at' => '2026-01-05']);
        BodyWeight::factory()->create(['recorded_at' => '2026-01-04']);
        BodyWeight::factory()->create(['recorded_at' => '2026-01-03']);
        BodyWeight::factory()->create(['recorded_at' => '2026-01-02']);
        BodyWeight::factory()->create(['recorded_at' => '2026-01-01']);
        BodyWeight::factory()->create(['recorded_at' => '2025-12-31']);

        expect($this->service->check(Achievement::WeighIn7Days))->toBeTrue();

        Carbon::setTestNow();
    });
});

describe('progress tracking', function () {
    it('returns correct progress for partially completed achievements', function () {
        Workout::factory()->count(5)->create(['status' => WorkoutStatus::Completed]);

        $progress = $this->service->getProgress(Achievement::Workouts10);

        expect($progress)
            ->current->toBe(5)
            ->threshold->toBe(10)
            ->percentage->toBe(50);
    });

    it('caps progress percentage at 100', function () {
        Workout::factory()->count(15)->create(['status' => WorkoutStatus::Completed]);

        $progress = $this->service->getProgress(Achievement::Workouts10);

        expect($progress['percentage'])->toBe(100);
    });
});

describe('unlocking achievements', function () {
    it('creates user achievement record when unlocking', function () {
        $achievement = $this->service->unlock(Achievement::FirstWorkout);

        expect($achievement)->not->toBeNull();
        expect(UserAchievement::query()->where('achievement', Achievement::FirstWorkout->value)->exists())->toBeTrue();
    });

    it('does not create duplicate achievement records', function () {
        $this->service->unlock(Achievement::FirstWorkout);
        $second = $this->service->unlock(Achievement::FirstWorkout);

        expect($second)->toBeNull();
        expect(UserAchievement::query()->where('achievement', Achievement::FirstWorkout->value)->count())->toBe(1);
    });

    it('checks and unlocks all eligible achievements', function () {
        // Create without triggering model boot by using query builder
        \Illuminate\Support\Facades\DB::table('workouts')->insert([
            'status' => WorkoutStatus::Completed->value,
            'scheduled_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('personal_records')->insert([
            'exercise_id' => Exercise::factory()->create()->id,
            'type' => 'max_weight',
            'value' => 100,
            'achieved_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('body_weights')->insert([
            'recorded_at' => now(),
            'stones' => 12,
            'pounds' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $unlocked = $this->service->checkAndUnlockAll();

        expect($unlocked->pluck('achievement.value'))
            ->toContain(Achievement::FirstWorkout->value)
            ->toContain(Achievement::FirstPR->value)
            ->toContain(Achievement::FirstWeighIn->value);
    });
});

describe('almost achieved', function () {
    it('returns achievements at 75% or more progress', function () {
        // 8 workouts = 80% of 10 needed for Workouts10
        Workout::factory()->count(8)->create(['status' => WorkoutStatus::Completed]);

        $almost = $this->service->getAlmostAchieved();

        $values = $almost->pluck('achievement.value')->toArray();
        expect($values)->toContain(Achievement::Workouts10->value);
    });

    it('does not return achievements below 75% progress', function () {
        // 7 workouts = 70% of 10 needed
        Workout::factory()->count(7)->create(['status' => WorkoutStatus::Completed]);

        $almost = $this->service->getAlmostAchieved();

        $values = $almost->pluck('achievement.value')->toArray();
        expect($values)->not->toContain(Achievement::Workouts10->value);
    });

    it('does not return already unlocked achievements', function () {
        Workout::factory()->count(15)->create(['status' => WorkoutStatus::Completed]);
        $this->service->unlock(Achievement::Workouts10);

        $almost = $this->service->getAlmostAchieved();

        $values = $almost->pluck('achievement.value')->toArray();
        expect($values)->not->toContain(Achievement::Workouts10->value);
    });

    it('limits results to specified count', function () {
        // Create many workouts to get multiple achievements at different progress levels
        Workout::factory()->count(40)->create(['status' => WorkoutStatus::Completed]);

        $almost = $this->service->getAlmostAchieved(3);

        expect($almost->count())->toBeLessThanOrEqual(3);
    });
});

describe('get for category', function () {
    it('returns all achievements for a category with status', function () {
        $this->service->unlock(Achievement::FirstWorkout);

        $consistency = $this->service->getForCategory(AchievementCategory::Consistency);

        $firstWorkout = $consistency->firstWhere('achievement', Achievement::FirstWorkout);

        expect($firstWorkout)
            ->unlocked->toBeTrue()
            ->achieved_at->not->toBeNull();
    });

    it('includes locked achievements with progress', function () {
        Workout::factory()->count(5)->create(['status' => WorkoutStatus::Completed]);

        $consistency = $this->service->getForCategory(AchievementCategory::Consistency);

        $workouts10 = $consistency->firstWhere('achievement', Achievement::Workouts10);

        expect($workouts10)
            ->unlocked->toBeFalse()
            ->progress->current->toBe(5);
    });
});
