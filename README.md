# GymBro

A personal gym workout tracking application built with Laravel 12 and Filament 4.

## Features

- **Workout Tracking** - Log workouts with sets, reps, and weights for strength exercises, plus duration and distance for cardio
- **Exercise Library** - Manage exercises organized by muscle group (chest, back, shoulders, biceps, triceps, quads, hamstrings, glutes, core) and type (strength, cardio, timed)
- **Workout Templates** - Create reusable workout templates with target sets, reps, rest periods, and notes
- **Weekly Scheduling** - Schedule workout templates for specific days of the week
- **Personal Records** - Track PRs for max weight, max reps, and max volume per exercise
- **Body Weight Tracking** - Log body weight measurements over time with optional progress photos
- **Dashboard Widgets** - View workout stats, body weight trends, exercise progress charts, and recent PRs

## Tech Stack

- **PHP 8.4** with Laravel 12
- **Filament 4** for the admin panel UI
- **Livewire 3** for reactive components
- **Tailwind CSS 4** for styling
- **SQLite** for the database
- **Pest 4** for testing

## Installation

```bash
# Clone the repository
git clone <repo-url> gymbro
cd gymbro

# Run the setup script
composer setup
```

The setup script will:
- Install Composer dependencies
- Copy `.env.example` to `.env`
- Generate an application key
- Run database migrations
- Install npm dependencies
- Build frontend assets

## Development

```bash
# Start all development services (server, queue, logs, vite)
composer dev
```

This runs the Laravel dev server, queue worker, log viewer (Pail), and Vite in parallel.

## Testing

```bash
# Run all tests
composer test

# Or directly with artisan
php artisan test

# Run specific test file
php artisan test tests/Feature/WorkoutResourceTest.php

# Filter by test name
php artisan test --filter="can create workout"
```

## Project Structure

```
app/
├── Actions/           # Business logic action classes
│   ├── Workouts/      # StartWorkout, CompleteWorkout, SkipWorkout, etc.
│   └── WeeklySchedules/
├── Enums/             # ExerciseType, MuscleGroup, PersonalRecordType, WorkoutStatus
├── Filament/          # Filament admin panel resources
│   ├── Admin/         # Shared columns and fields
│   ├── Pages/         # Dashboard
│   ├── Resources/     # CRUD resources for each model
│   └── Widgets/       # Dashboard widgets and charts
└── Models/            # Eloquent models

tests/
├── Feature/           # Feature tests for Filament resources
└── Unit/              # Unit tests for actions and models
```

## Models

| Model | Description |
|-------|-------------|
| `Exercise` | An exercise with name, muscle group, and type |
| `WorkoutTemplate` | A reusable template defining exercises with targets |
| `WeeklySchedule` | Links a template to a day of the week |
| `Workout` | An actual workout session with status and timestamps |
| `WorkoutSet` | A single set within a workout (exercise, weight, reps) |
| `CardioLog` | Cardio activity log (duration, distance, calories) |
| `PersonalRecord` | A PR achievement for an exercise |
| `BodyWeight` | A body weight measurement entry |

## License

MIT
