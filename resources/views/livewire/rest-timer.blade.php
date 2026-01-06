<div
    x-data="{
        seconds: @entangle('seconds'),
        totalSeconds: @entangle('totalSeconds'),
        isRunning: @entangle('isRunning'),
        interval: null,
        
        get formattedTime() {
            const mins = Math.floor(this.seconds / 60);
            const secs = this.seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },
        
        get progress() {
            if (this.totalSeconds === 0) return 0;
            return ((this.totalSeconds - this.seconds) / this.totalSeconds) * 100;
        },
        
        startCountdown() {
            if (this.interval) return;
            
            this.interval = setInterval(() => {
                if (this.seconds > 0) {
                    this.seconds--;
                } else {
                    this.stopCountdown();
                    this.vibrate();
                    $wire.complete();
                }
            }, 1000);
        },
        
        stopCountdown() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        },
        
        vibrate() {
            if ('vibrate' in navigator) {
                // Vibration pattern: vibrate 500ms, pause 200ms, vibrate 500ms, pause 200ms, vibrate 500ms
                navigator.vibrate([500, 200, 500, 200, 500]);
            }
        }
    }"
    x-init="
        $watch('isRunning', (running) => {
            if (running) {
                startCountdown();
            } else {
                stopCountdown();
            }
        });
        
        Livewire.on('timer-started', () => {
            startCountdown();
        });
        
        Livewire.on('timer-stopped', () => {
            stopCountdown();
        });
        
        Livewire.on('timer-reset', () => {
            stopCountdown();
        });
    "
    class="flex flex-col items-center gap-4 py-4"
>
    <!-- Timer Display -->
    <div class="relative w-40 h-40 sm:w-48 sm:h-48">
        <!-- Progress Ring -->
        <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
            <!-- Background circle -->
            <circle
                cx="50"
                cy="50"
                r="45"
                fill="none"
                stroke="currentColor"
                stroke-width="8"
                class="text-gray-200 dark:text-gray-700"
            />
            <!-- Progress circle -->
            <circle
                cx="50"
                cy="50"
                r="45"
                fill="none"
                stroke="currentColor"
                stroke-width="8"
                stroke-linecap="round"
                class="text-primary-500 transition-all duration-1000"
                :stroke-dasharray="283"
                :stroke-dashoffset="283 - (283 * progress / 100)"
            />
        </svg>
        
        <!-- Time Display -->
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span
                x-text="formattedTime"
                class="text-3xl sm:text-4xl font-bold tabular-nums"
                :class="seconds <= 5 && isRunning ? 'text-red-500 animate-pulse' : 'text-gray-900 dark:text-white'"
            ></span>
            <span class="text-sm text-gray-500 dark:text-gray-400" x-show="isRunning">
                Rest
            </span>
        </div>
    </div>
    
    <!-- Controls -->
    <div class="flex items-center gap-3">
        <!-- Subtract Time -->
        <button
            type="button"
            wire:click="subtractTime(15)"
            class="flex items-center justify-center w-10 h-10 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
            title="Subtract 15 seconds"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
            </svg>
        </button>
        
        <!-- Start/Stop Button -->
        <button
            type="button"
            x-show="!isRunning"
            wire:click="start"
            class="flex items-center justify-center w-14 h-14 bg-primary-500 hover:bg-primary-600 text-white rounded-full shadow-md transition-colors"
        >
            <svg class="w-7 h-7 ml-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
            </svg>
        </button>
        
        <button
            type="button"
            x-show="isRunning"
            wire:click="stop"
            class="flex items-center justify-center w-14 h-14 bg-red-500 hover:bg-red-600 text-white rounded-full shadow-md transition-colors"
        >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 6h12v12H6z" />
            </svg>
        </button>
        
        <!-- Add Time -->
        <button
            type="button"
            wire:click="addTime(15)"
            class="flex items-center justify-center w-10 h-10 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
            title="Add 15 seconds"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>
    </div>
    
    <!-- Reset Button -->
    <button
        type="button"
        wire:click="resetTimer"
        x-show="!isRunning && seconds !== {{ $defaultSeconds }}"
        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline transition-colors"
    >
        Reset to {{ $defaultSeconds }}s
    </button>
    
    <!-- Quick Time Presets -->
    <div class="flex flex-wrap items-center justify-center gap-2">
        @foreach ([30, 45, 60, 90, 120] as $preset)
            <button
                type="button"
                wire:click="start({{ $preset }})"
                class="px-3 py-1.5 text-sm font-medium rounded-full transition-colors"
                :class="seconds === {{ $preset }} && !isRunning
                    ? 'bg-primary-500 text-white'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
            >
                {{ $preset }}s
            </button>
        @endforeach
    </div>
</div>
