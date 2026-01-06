<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkoutSet extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'set_number' => 'integer',
            'weight_kg' => 'decimal:2',
            'reps' => 'integer',
            'is_warmup' => 'boolean',
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

    public function personalRecord(): HasOne
    {
        return $this->hasOne(PersonalRecord::class);
    }

    protected function volume(): Attribute
    {
        return Attribute::get(fn () => ($this->weight_kg ?? 0) * ($this->reps ?? 0));
    }
}
