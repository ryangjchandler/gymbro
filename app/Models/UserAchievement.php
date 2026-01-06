<?php

namespace App\Models;

use App\Enums\Achievement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    /** @use HasFactory<\Database\Factories\UserAchievementFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'achievement' => Achievement::class,
            'achieved_at' => 'datetime',
        ];
    }
}
