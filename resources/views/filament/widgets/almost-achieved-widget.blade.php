<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon
                    icon="heroicon-o-arrow-trending-up"
                    class="h-5 w-5 text-primary-500"
                />
                <span>Almost There!</span>
            </div>
        </x-slot>

        @php
            $almostAchieved = $this->getAlmostAchieved();
        @endphp

        @if ($almostAchieved->isEmpty())
            <div class="text-center py-6">
                <x-filament::icon
                    icon="heroicon-o-trophy"
                    class="mx-auto h-12 w-12 text-gray-400"
                />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No achievements in progress</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Keep working out to progress toward achievements!</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($almostAchieved as $item)
                    @php
                        $achievement = $item['achievement'];
                        $progress = $item['progress'];
                        $tier = $achievement->getTier();
                    @endphp
                    <div class="flex items-center gap-4">
                        <div 
                            @class([
                                'flex shrink-0 items-center justify-center w-10 h-10 rounded-full',
                                $tier->getBackgroundColor(),
                            ])
                        >
                            <x-filament::icon
                                :icon="$achievement->getCategory()->getIcon()"
                                @class([
                                    'h-5 w-5',
                                    $tier->getIconColor(),
                                ])
                            />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900 dark:text-white truncate">
                                    {{ $achievement->getLabel() }}
                                </p>
                                <span class="shrink-0 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $progress['percentage'] }}%
                                </span>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                <div 
                                    @class([
                                        'h-2 rounded-full transition-all duration-300',
                                        'bg-amber-500' => $tier->value === 'bronze',
                                        'bg-gray-400' => $tier->value === 'silver',
                                        'bg-yellow-500' => $tier->value === 'gold',
                                        'bg-cyan-400' => $tier->value === 'platinum',
                                        'bg-red-500' => $tier->value === 'ruby',
                                        'bg-purple-500' => $tier->value === 'diamond',
                                    ])
                                    style="width: {{ $progress['percentage'] }}%"
                                ></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ number_format($progress['current']) }} / {{ number_format($progress['threshold']) }} - {{ $achievement->getDescription() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
