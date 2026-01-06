<?php

namespace App\Models;

use App\Services\AchievementService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BodyWeight extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function () {
            app(AchievementService::class)->checkAndUnlockAll();
        });
    }

    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
            'stones' => 'integer',
            'pounds' => 'decimal:1',
        ];
    }

    protected function formattedWeight(): Attribute
    {
        return Attribute::get(function () {
            $pounds = $this->pounds == floor($this->pounds)
                ? (int) $this->pounds
                : $this->pounds;

            return "{$this->stones}st {$pounds}lbs";
        });
    }

    protected function totalPounds(): Attribute
    {
        return Attribute::get(fn () => ($this->stones * 14) + $this->pounds);
    }

    protected function totalKilograms(): Attribute
    {
        return Attribute::get(fn () => round($this->total_pounds * 0.453592, 1));
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->photo_path) {
                return null;
            }

            return Storage::url($this->photo_path);
        });
    }
}
