<x-filament-widgets::widget>
    @if ($activeWorkout)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon
                        icon="heroicon-o-bolt"
                        class="h-5 w-5 text-primary-500"
                    />
                    <span>Active Workout</span>
                </div>
            </x-slot>

            @if ($activeWorkout->started_at)
                <x-slot name="headerEnd">
                    <div
                        x-data="{
                            startTime: {{ $activeWorkout->started_at->timestamp * 1000 }},
                            elapsed: '',
                            interval: null,
                            
                            formatDuration(ms) {
                                const totalSeconds = Math.floor(ms / 1000);
                                const hours = Math.floor(totalSeconds / 3600);
                                const minutes = Math.floor((totalSeconds % 3600) / 60);
                                const seconds = totalSeconds % 60;
                                
                                if (hours > 0) {
                                    return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                                }
                                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
                            },
                            
                            updateElapsed() {
                                this.elapsed = this.formatDuration(Date.now() - this.startTime);
                            },
                            
                            init() {
                                this.updateElapsed();
                                this.interval = setInterval(() => this.updateElapsed(), 1000);
                            },
                            
                            destroy() {
                                if (this.interval) clearInterval(this.interval);
                            }
                        }"
                        class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400"
                    >
                        <x-filament::icon
                            icon="heroicon-o-clock"
                            class="h-4 w-4"
                        />
                        <span x-text="elapsed" class="tabular-nums font-medium"></span>
                    </div>
                </x-slot>
            @endif

            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Left Column: Rest Timer --}}
                <div class="flex flex-col">
                    <div class="rounded-xl bg-gray-50 dark:bg-white/5 p-4">
                        @livewire(\App\Livewire\RestTimer::class)
                    </div>
                </div>

                {{-- Right Column: Workout Info & Exercises --}}
                <div class="flex flex-col gap-6">
                    {{-- Workout Info --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $activeWorkout->workoutTemplate?->name ?? 'Workout' }}
                        </h3>
                        @if ($activeWorkout->workoutTemplate?->description)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $activeWorkout->workoutTemplate->description }}
                            </p>
                        @endif
                        @if ($activeWorkout->started_at)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Started {{ $activeWorkout->started_at->format('g:i A') }}
                            </p>
                        @endif

                        <div class="flex items-center gap-2 mt-4">
                            {{ $this->skipWorkoutAction }}
                            {{ $this->completeWorkoutAction }}
                        </div>
                    </div>

                    {{-- Exercises --}}
                    @if ($activeWorkout->workoutTemplate?->workoutTemplateExercises->count())
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Exercises</h4>
                            <div class="space-y-2">
                                @foreach ($activeWorkout->workoutTemplate->workoutTemplateExercises as $templateExercise)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-white/5 rounded-lg">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <span class="flex shrink-0 items-center justify-center w-6 h-6 text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 rounded-full">
                                                {{ $loop->iteration }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $templateExercise->exercise->name }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $templateExercise->target_sets }} sets
                                                    @if ($templateExercise->target_reps)
                                                        &times; {{ $templateExercise->target_reps }} reps
                                                    @endif
                                                    @if ($templateExercise->rest_seconds)
                                                        &bull; {{ $templateExercise->rest_seconds }}s rest
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <span class="shrink-0 ml-2 text-xs px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                            {{ $templateExercise->exercise->muscle_group->getLabel() }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-filament::section>

        <x-filament-actions::modals />
    @endif
</x-filament-widgets::widget>
