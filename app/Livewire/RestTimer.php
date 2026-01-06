<?php

namespace App\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;

class RestTimer extends Component
{
    public const DEFAULT_REST_SECONDS = 45;

    #[Locked]
    public int $defaultSeconds = self::DEFAULT_REST_SECONDS;

    public int $seconds = self::DEFAULT_REST_SECONDS;

    public int $totalSeconds = self::DEFAULT_REST_SECONDS;

    public bool $isRunning = false;

    public function mount(int $seconds = self::DEFAULT_REST_SECONDS): void
    {
        $this->defaultSeconds = $seconds;
        $this->seconds = $seconds;
        $this->totalSeconds = $seconds;
    }

    public function start(?int $seconds = null): void
    {
        if ($seconds !== null) {
            $this->seconds = $seconds;
        }

        $this->totalSeconds = $this->seconds;
        $this->isRunning = true;
        $this->dispatch('timer-started', seconds: $this->seconds);
    }

    public function stop(): void
    {
        $this->isRunning = false;
        $this->dispatch('timer-stopped');
    }

    public function resetTimer(): void
    {
        $this->seconds = $this->defaultSeconds;
        $this->totalSeconds = $this->defaultSeconds;
        $this->isRunning = false;
        $this->dispatch('timer-reset');
    }

    public function complete(): void
    {
        $this->isRunning = false;
        $this->dispatch('timer-completed');
    }

    public function addTime(int $seconds = 15): void
    {
        $this->seconds += $seconds;
        $this->dispatch('timer-adjusted', seconds: $this->seconds);
    }

    public function subtractTime(int $seconds = 15): void
    {
        $this->seconds = max(0, $this->seconds - $seconds);
        $this->dispatch('timer-adjusted', seconds: $this->seconds);
    }

    public function render()
    {
        return view('livewire.rest-timer');
    }
}
