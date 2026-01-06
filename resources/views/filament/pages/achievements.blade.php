<x-filament-panels::page>
    {{-- Header Stats --}}
    <div class="mb-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ $this->getUnlockedCount() }} of {{ $this->getTotalCount() }} achievements unlocked
        </p>
        <div class="mt-2 w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
            <div 
                class="bg-primary-600 h-2 rounded-full transition-all duration-500" 
                style="width: {{ ($this->getUnlockedCount() / $this->getTotalCount()) * 100 }}%"
            ></div>
        </div>
    </div>

    {{-- Categories as Horizontal Rows --}}
    <div class="space-y-6">
        @foreach ($this->getCategories() as $category)
            <div>
                {{-- Category Header --}}
                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                    <x-filament::icon
                        :icon="$category->getIcon()"
                        class="h-5 w-5 text-gray-500 dark:text-gray-400"
                    />
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $category->getLabel() }}
                    </h2>
                </div>

                {{-- Achievements Grid (5-6 per row) --}}
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                    @foreach ($this->getAchievementsForCategory($category) as $item)
                        @php
                            $achievement = $item['achievement'];
                            $unlocked = $item['unlocked'];
                            $achievedAt = $item['achieved_at'];
                            $progress = $item['progress'];
                            $tier = $achievement->getTier();
                        @endphp

                        <div 
                            @class([
                                'relative rounded-lg border p-3 transition-all duration-200',
                                'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' => $unlocked,
                                'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 opacity-60' => !$unlocked,
                            ])
                        >
                            {{-- Achievement Header --}}
                            <div class="flex items-center gap-2 mb-2">
                                <div 
                                    @class([
                                        'flex shrink-0 items-center justify-center w-7 h-7 rounded-full',
                                        'bg-green-500' => $unlocked,
                                        'bg-gray-200 dark:bg-gray-700' => !$unlocked,
                                    ])
                                >
                                    @if ($unlocked)
                                        <x-filament::icon
                                            icon="heroicon-s-check"
                                            class="h-4 w-4 text-white"
                                        />
                                    @else
                                        <x-filament::icon
                                            icon="heroicon-o-lock-closed"
                                            class="h-3.5 w-3.5 text-gray-400 dark:text-gray-500"
                                        />
                                    @endif
                                </div>

                                <span 
                                    @class([
                                        'text-[10px] px-1.5 py-0.5 rounded font-medium',
                                        $tier->getBackgroundColor(),
                                        $tier->getIconColor(),
                                    ])
                                >
                                    {{ $tier->getLabel() }}
                                </span>
                            </div>

                            {{-- Title --}}
                            <h3 
                                @class([
                                    'text-sm font-medium leading-tight',
                                    'text-gray-900 dark:text-white' => $unlocked,
                                    'text-gray-500 dark:text-gray-400' => !$unlocked,
                                ])
                            >
                                {{ $achievement->getLabel() }}
                            </h3>

                            {{-- Description --}}
                            <p 
                                @class([
                                    'text-xs mt-1 leading-tight',
                                    'text-gray-600 dark:text-gray-300' => $unlocked,
                                    'text-gray-400 dark:text-gray-500' => !$unlocked,
                                ])
                            >
                                {{ $achievement->getDescription() }}
                            </p>

                            {{-- Progress or Achieved Date --}}
                            @if ($unlocked && $achievedAt)
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-2">
                                    {{ $achievedAt->format('M j, Y') }}
                                </p>
                            @elseif (!$unlocked)
                                <div class="mt-2">
                                    <div class="flex items-center justify-between text-[10px] text-gray-500 dark:text-gray-400 mb-1">
                                        <span>{{ number_format($progress['current']) }} / {{ number_format($progress['threshold']) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1 dark:bg-gray-600">
                                        <div 
                                            class="bg-gray-400 dark:bg-gray-500 h-1 rounded-full transition-all duration-300" 
                                            style="width: {{ $progress['percentage'] }}%"
                                        ></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
