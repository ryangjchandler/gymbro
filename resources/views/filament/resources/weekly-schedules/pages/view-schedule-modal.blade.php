<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ $schedule->workoutTemplate->name }}
        </h3>
        @if ($schedule->workoutTemplate->description)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $schedule->workoutTemplate->description }}
            </p>
        @endif

        <div class="mt-3 flex items-center gap-2">
            @if ($schedule->is_active)
                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/50 dark:text-green-400">
                    Active
                </span>
            @else
                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/50 dark:text-amber-400">
                    Paused
                </span>
            @endif
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $schedule->workoutTemplate->workoutTemplateExercises->count() }} exercises
            </span>
        </div>
    </div>

    {{-- Exercise List --}}
    @if ($schedule->workoutTemplate->workoutTemplateExercises->count())
        <div>
            <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Exercises</h4>
            <div class="space-y-2">
                @foreach ($schedule->workoutTemplate->workoutTemplateExercises as $templateExercise)
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-white/5">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                {{ $loop->iteration }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900 dark:text-white">
                                    {{ $templateExercise->exercise->name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if ($templateExercise->target_sets)
                                        {{ $templateExercise->target_sets }} sets
                                    @endif
                                    @if ($templateExercise->target_reps)
                                        &times; {{ $templateExercise->target_reps }} reps
                                    @endif
                                    @if ($templateExercise->target_duration_seconds)
                                        @if ($templateExercise->target_sets || $templateExercise->target_reps)
                                            &bull;
                                        @endif
                                        {{ floor($templateExercise->target_duration_seconds / 60) }}:{{ str_pad($templateExercise->target_duration_seconds % 60, 2, '0', STR_PAD_LEFT) }}
                                    @endif
                                    @if ($templateExercise->rest_seconds)
                                        &bull; {{ $templateExercise->rest_seconds }}s rest
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="ml-2 shrink-0 rounded-full bg-gray-200 px-2 py-1 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                            {{ $templateExercise->exercise->muscle_group->getLabel() }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="rounded-lg bg-gray-50 p-6 text-center dark:bg-white/5">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No exercises added to this template yet.
            </p>
        </div>
    @endif
</div>
