<x-filament-panels::page>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4 lg:grid-cols-7">
        @foreach ($this->getOrderedDays() as $day)
            @php
                $schedule = $this->getScheduleForDay($day);
                $isToday = now()->dayOfWeek === $day;
            @endphp

            <button
                type="button"
                wire:click="{{ $schedule ? "selectDay({$day})" : "selectEmptyDay({$day})" }}"
                @class([
                    'flex flex-col items-start rounded-xl p-4 text-left transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900',
                    'bg-gray-100 dark:bg-gray-800 ring-2 ring-primary-500/50' => $isToday,
                    'bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700 hover:ring-gray-300 dark:hover:ring-gray-600' => ! $isToday,
                    'opacity-60' => $schedule && ! $schedule->is_active,
                ])
            >
                {{-- Day name header --}}
                <h3 @class([
                    'text-sm',
                    'font-bold text-gray-900 dark:text-white' => $isToday,
                    'font-medium text-gray-500 dark:text-gray-400' => ! $isToday,
                ])>
                    {{ $this->getDayName($day) }}
                </h3>

                @if ($isToday)
                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400">
                        Today
                    </span>
                @endif

                <div class="mt-3 w-full">
                    @if ($schedule)
                        {{-- Scheduled workout --}}
                        <p class="font-semibold text-gray-900 dark:text-white truncate">
                            {{ $schedule->workoutTemplate->name }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $schedule->workoutTemplate->workoutTemplateExercises->count() }} exercises
                        </p>
                        @unless ($schedule->is_active)
                            <span class="mt-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/50 dark:text-amber-400">
                                Paused
                            </span>
                        @endunless
                    @else
                        {{-- Rest day --}}
                        <p class="text-sm text-gray-400 dark:text-gray-600">
                            Rest Day
                        </p>
                    @endif
                </div>
            </button>
        @endforeach
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
