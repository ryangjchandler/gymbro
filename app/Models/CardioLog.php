<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardioLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'speed' => 'decimal:1',
            'distance_km' => 'decimal:2',
        ];
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    protected function durationForHumans(): Attribute
    {
        return Attribute::get(function () {
            $minutes = (int) floor($this->duration_seconds / 60);
            $seconds = $this->duration_seconds % 60;

            if ($minutes === 0) {
                return "{$seconds}s";
            }

            return $seconds > 0 ? "{$minutes}m {$seconds}s" : "{$minutes}m";
        });
    }
}
