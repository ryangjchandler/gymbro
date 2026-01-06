<?php

namespace App\Models;

use App\Enums\PersonalRecordType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalRecord extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => PersonalRecordType::class,
            'value' => 'decimal:2',
            'achieved_at' => 'date',
        ];
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function workoutSet(): BelongsTo
    {
        return $this->belongsTo(WorkoutSet::class);
    }

    protected function formattedValue(): Attribute
    {
        return Attribute::get(fn () => match ($this->type) {
            PersonalRecordType::MaxWeight => number_format($this->value, 1).'kg',
            PersonalRecordType::MaxReps => (int) $this->value.' reps',
            PersonalRecordType::MaxVolume => number_format($this->value, 0).'kg',
        });
    }
}
