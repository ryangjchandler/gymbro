<?php

namespace Database\Seeders;

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // Chest
            ['name' => 'Bench Press', 'muscle_group' => MuscleGroup::Chest, 'type' => ExerciseType::Strength],
            ['name' => 'Incline Bench Press', 'muscle_group' => MuscleGroup::Chest, 'type' => ExerciseType::Strength],
            ['name' => 'Decline Bench Press', 'muscle_group' => MuscleGroup::Chest, 'type' => ExerciseType::Strength],
            ['name' => 'Dumbbell Fly', 'muscle_group' => MuscleGroup::Chest, 'type' => ExerciseType::Strength],
            ['name' => 'Incline Dumbbell Press', 'muscle_group' => MuscleGroup::Chest, 'type' => ExerciseType::Strength],
            ['name' => 'Cable Crossover', 'muscle_group' => MuscleGroup::Chest, 'type' => ExerciseType::Strength],
            ['name' => 'Push-ups', 'muscle_group' => MuscleGroup::Chest, 'type' => ExerciseType::Strength],
            ['name' => 'Chest Dips', 'muscle_group' => MuscleGroup::Chest, 'type' => ExerciseType::Strength],

            // Back
            ['name' => 'Deadlift', 'muscle_group' => MuscleGroup::Back, 'type' => ExerciseType::Strength],
            ['name' => 'Barbell Row', 'muscle_group' => MuscleGroup::Back, 'type' => ExerciseType::Strength],
            ['name' => 'Pull-ups', 'muscle_group' => MuscleGroup::Back, 'type' => ExerciseType::Strength],
            ['name' => 'Lat Pulldown', 'muscle_group' => MuscleGroup::Back, 'type' => ExerciseType::Strength],
            ['name' => 'Seated Cable Row', 'muscle_group' => MuscleGroup::Back, 'type' => ExerciseType::Strength],
            ['name' => 'T-Bar Row', 'muscle_group' => MuscleGroup::Back, 'type' => ExerciseType::Strength],
            ['name' => 'Single Arm Dumbbell Row', 'muscle_group' => MuscleGroup::Back, 'type' => ExerciseType::Strength],
            ['name' => 'Chin-ups', 'muscle_group' => MuscleGroup::Back, 'type' => ExerciseType::Strength],

            // Shoulders
            ['name' => 'Overhead Press', 'muscle_group' => MuscleGroup::Shoulders, 'type' => ExerciseType::Strength],
            ['name' => 'Dumbbell Shoulder Press', 'muscle_group' => MuscleGroup::Shoulders, 'type' => ExerciseType::Strength],
            ['name' => 'Lateral Raise', 'muscle_group' => MuscleGroup::Shoulders, 'type' => ExerciseType::Strength],
            ['name' => 'Front Raise', 'muscle_group' => MuscleGroup::Shoulders, 'type' => ExerciseType::Strength],
            ['name' => 'Face Pull', 'muscle_group' => MuscleGroup::Shoulders, 'type' => ExerciseType::Strength],
            ['name' => 'Shrugs', 'muscle_group' => MuscleGroup::Shoulders, 'type' => ExerciseType::Strength],
            ['name' => 'Arnold Press', 'muscle_group' => MuscleGroup::Shoulders, 'type' => ExerciseType::Strength],
            ['name' => 'Reverse Fly', 'muscle_group' => MuscleGroup::Shoulders, 'type' => ExerciseType::Strength],

            // Biceps
            ['name' => 'Barbell Curl', 'muscle_group' => MuscleGroup::Biceps, 'type' => ExerciseType::Strength],
            ['name' => 'Dumbbell Curl', 'muscle_group' => MuscleGroup::Biceps, 'type' => ExerciseType::Strength],
            ['name' => 'Hammer Curl', 'muscle_group' => MuscleGroup::Biceps, 'type' => ExerciseType::Strength],
            ['name' => 'Preacher Curl', 'muscle_group' => MuscleGroup::Biceps, 'type' => ExerciseType::Strength],
            ['name' => 'Cable Curl', 'muscle_group' => MuscleGroup::Biceps, 'type' => ExerciseType::Strength],
            ['name' => 'Concentration Curl', 'muscle_group' => MuscleGroup::Biceps, 'type' => ExerciseType::Strength],

            // Triceps
            ['name' => 'Tricep Pushdown', 'muscle_group' => MuscleGroup::Triceps, 'type' => ExerciseType::Strength],
            ['name' => 'Skull Crushers', 'muscle_group' => MuscleGroup::Triceps, 'type' => ExerciseType::Strength],
            ['name' => 'Overhead Tricep Extension', 'muscle_group' => MuscleGroup::Triceps, 'type' => ExerciseType::Strength],
            ['name' => 'Tricep Dips', 'muscle_group' => MuscleGroup::Triceps, 'type' => ExerciseType::Strength],
            ['name' => 'Close-Grip Bench Press', 'muscle_group' => MuscleGroup::Triceps, 'type' => ExerciseType::Strength],
            ['name' => 'Diamond Push-ups', 'muscle_group' => MuscleGroup::Triceps, 'type' => ExerciseType::Strength],

            // Quads
            ['name' => 'Squat', 'muscle_group' => MuscleGroup::Quads, 'type' => ExerciseType::Strength],
            ['name' => 'Front Squat', 'muscle_group' => MuscleGroup::Quads, 'type' => ExerciseType::Strength],
            ['name' => 'Leg Press', 'muscle_group' => MuscleGroup::Quads, 'type' => ExerciseType::Strength],
            ['name' => 'Lunges', 'muscle_group' => MuscleGroup::Quads, 'type' => ExerciseType::Strength],
            ['name' => 'Leg Extension', 'muscle_group' => MuscleGroup::Quads, 'type' => ExerciseType::Strength],
            ['name' => 'Bulgarian Split Squat', 'muscle_group' => MuscleGroup::Quads, 'type' => ExerciseType::Strength],
            ['name' => 'Hack Squat', 'muscle_group' => MuscleGroup::Quads, 'type' => ExerciseType::Strength],

            // Hamstrings
            ['name' => 'Romanian Deadlift', 'muscle_group' => MuscleGroup::Hamstrings, 'type' => ExerciseType::Strength],
            ['name' => 'Lying Leg Curl', 'muscle_group' => MuscleGroup::Hamstrings, 'type' => ExerciseType::Strength],
            ['name' => 'Seated Leg Curl', 'muscle_group' => MuscleGroup::Hamstrings, 'type' => ExerciseType::Strength],
            ['name' => 'Good Mornings', 'muscle_group' => MuscleGroup::Hamstrings, 'type' => ExerciseType::Strength],
            ['name' => 'Nordic Curl', 'muscle_group' => MuscleGroup::Hamstrings, 'type' => ExerciseType::Strength],

            // Glutes
            ['name' => 'Hip Thrust', 'muscle_group' => MuscleGroup::Glutes, 'type' => ExerciseType::Strength],
            ['name' => 'Glute Bridge', 'muscle_group' => MuscleGroup::Glutes, 'type' => ExerciseType::Strength],
            ['name' => 'Cable Kickback', 'muscle_group' => MuscleGroup::Glutes, 'type' => ExerciseType::Strength],
            ['name' => 'Sumo Deadlift', 'muscle_group' => MuscleGroup::Glutes, 'type' => ExerciseType::Strength],

            // Core
            ['name' => 'Plank', 'muscle_group' => MuscleGroup::Core, 'type' => ExerciseType::Timed],
            ['name' => 'Side Plank', 'muscle_group' => MuscleGroup::Core, 'type' => ExerciseType::Timed],
            ['name' => 'Crunches', 'muscle_group' => MuscleGroup::Core, 'type' => ExerciseType::Strength],
            ['name' => 'Hanging Leg Raise', 'muscle_group' => MuscleGroup::Core, 'type' => ExerciseType::Strength],
            ['name' => 'Cable Woodchop', 'muscle_group' => MuscleGroup::Core, 'type' => ExerciseType::Strength],
            ['name' => 'Ab Rollout', 'muscle_group' => MuscleGroup::Core, 'type' => ExerciseType::Strength],
            ['name' => 'Russian Twist', 'muscle_group' => MuscleGroup::Core, 'type' => ExerciseType::Strength],
            ['name' => 'Dead Bug', 'muscle_group' => MuscleGroup::Core, 'type' => ExerciseType::Timed],

            // Cardio
            ['name' => 'Treadmill', 'muscle_group' => MuscleGroup::Cardio, 'type' => ExerciseType::Cardio],
            ['name' => 'Stationary Bike', 'muscle_group' => MuscleGroup::Cardio, 'type' => ExerciseType::Cardio],
            ['name' => 'Rowing Machine', 'muscle_group' => MuscleGroup::Cardio, 'type' => ExerciseType::Cardio],
            ['name' => 'Stair Climber', 'muscle_group' => MuscleGroup::Cardio, 'type' => ExerciseType::Cardio],
            ['name' => 'Elliptical', 'muscle_group' => MuscleGroup::Cardio, 'type' => ExerciseType::Cardio],
            ['name' => 'Jump Rope', 'muscle_group' => MuscleGroup::Cardio, 'type' => ExerciseType::Cardio],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}
