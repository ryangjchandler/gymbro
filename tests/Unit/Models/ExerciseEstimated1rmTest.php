<?php

use App\Models\Exercise;
use App\Models\WorkoutSet;

describe('calculateEpley1rm', function () {
    it('returns the weight for 1 rep', function () {
        expect(Exercise::calculateEpley1rm(100, 1))->toBe(100.0);
    });

    it('calculates correctly for 5 reps', function () {
        // Epley: 100 * (1 + 5/30) = 100 * 1.167 = 116.7
        expect(Exercise::calculateEpley1rm(100, 5))->toBe(116.7);
    });

    it('calculates correctly for 8 reps', function () {
        // Epley: 100 * (1 + 8/30) = 100 * 1.267 = 126.7
        expect(Exercise::calculateEpley1rm(100, 8))->toBe(126.7);
    });

    it('calculates correctly for 10 reps', function () {
        // Epley: 100 * (1 + 10/30) = 100 * 1.333 = 133.3
        expect(Exercise::calculateEpley1rm(100, 10))->toBe(133.3);
    });

    it('rounds to one decimal place', function () {
        // Epley: 87.5 * (1 + 6/30) = 87.5 * 1.2 = 105.0
        expect(Exercise::calculateEpley1rm(87.5, 6))->toBe(105.0);
    });
});

describe('getEstimated1rm', function () {
    it('returns null for cardio exercises', function () {
        $exercise = Exercise::factory()->cardio()->create();

        expect($exercise->getEstimated1rm())->toBeNull();
    });

    it('returns null for timed exercises', function () {
        $exercise = Exercise::factory()->timed()->create();

        expect($exercise->getEstimated1rm())->toBeNull();
    });

    it('returns null for strength exercises with no sets', function () {
        $exercise = Exercise::factory()->strength()->create();

        expect($exercise->getEstimated1rm())->toBeNull();
    });

    it('returns null when all sets have more than 10 reps', function () {
        $exercise = Exercise::factory()->strength()->create();

        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(100)
            ->withReps(12)
            ->create();

        expect($exercise->getEstimated1rm())->toBeNull();
    });

    it('returns null when all sets are warmups', function () {
        $exercise = Exercise::factory()->strength()->create();

        WorkoutSet::factory()
            ->for($exercise)
            ->warmup()
            ->withWeight(60)
            ->withReps(8)
            ->create();

        expect($exercise->getEstimated1rm())->toBeNull();
    });

    it('returns estimated 1RM for a single qualifying set', function () {
        $exercise = Exercise::factory()->strength()->create();

        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(100)
            ->withReps(8)
            ->create();

        $result = $exercise->getEstimated1rm();

        expect($result)
            ->not->toBeNull()
            ->weight_kg->toBe(100.0)
            ->reps->toBe(8)
            ->estimated_1rm->toBe(126.7);
    });

    it('returns the set with highest estimated 1RM', function () {
        $exercise = Exercise::factory()->strength()->create();

        // Set 1: 100kg x 8 = 126.7kg estimated 1RM
        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(100)
            ->withReps(8)
            ->create();

        // Set 2: 110kg x 5 = 128.3kg estimated 1RM (higher)
        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(110)
            ->withReps(5)
            ->create();

        // Set 3: 90kg x 10 = 120kg estimated 1RM
        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(90)
            ->withReps(10)
            ->create();

        $result = $exercise->getEstimated1rm();

        expect($result)
            ->weight_kg->toBe(110.0)
            ->reps->toBe(5)
            ->estimated_1rm->toBe(128.3);
    });

    it('excludes warmup sets from calculation', function () {
        $exercise = Exercise::factory()->strength()->create();

        // Warmup set with high weight (should be excluded)
        WorkoutSet::factory()
            ->for($exercise)
            ->warmup()
            ->withWeight(120)
            ->withReps(5)
            ->create();

        // Working set with lower weight
        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(100)
            ->withReps(8)
            ->create();

        $result = $exercise->getEstimated1rm();

        expect($result)
            ->weight_kg->toBe(100.0)
            ->reps->toBe(8);
    });

    it('excludes sets with more than 10 reps', function () {
        $exercise = Exercise::factory()->strength()->create();

        // Set with 12 reps (excluded)
        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(90)
            ->withReps(12)
            ->create();

        // Set with 8 reps (included)
        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(100)
            ->withReps(8)
            ->create();

        $result = $exercise->getEstimated1rm();

        expect($result)
            ->weight_kg->toBe(100.0)
            ->reps->toBe(8);
    });

    it('excludes sets with zero weight', function () {
        $exercise = Exercise::factory()->strength()->create();

        WorkoutSet::factory()
            ->for($exercise)
            ->withWeight(0)
            ->withReps(8)
            ->create();

        expect($exercise->getEstimated1rm())->toBeNull();
    });

    it('excludes sets with null weight', function () {
        $exercise = Exercise::factory()->strength()->create();

        WorkoutSet::factory()
            ->for($exercise)
            ->state(['weight_kg' => null])
            ->withReps(8)
            ->create();

        expect($exercise->getEstimated1rm())->toBeNull();
    });
});

describe('scopePinned', function () {
    it('returns only pinned exercises', function () {
        Exercise::factory()->count(3)->create(['is_pinned' => false]);
        $pinned = Exercise::factory()->count(2)->pinned()->create();

        $result = Exercise::pinned()->get();

        expect($result)->toHaveCount(2);
        expect($result->pluck('id')->toArray())
            ->toEqualCanonicalizing($pinned->pluck('id')->toArray());
    });
});

describe('scopeStrength', function () {
    it('returns only strength exercises', function () {
        Exercise::factory()->cardio()->create();
        Exercise::factory()->timed()->create();
        $strength = Exercise::factory()->count(2)->strength()->create();

        $result = Exercise::strength()->get();

        expect($result)->toHaveCount(2);
        expect($result->pluck('id')->toArray())
            ->toEqualCanonicalizing($strength->pluck('id')->toArray());
    });
});
