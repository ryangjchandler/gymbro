<?php

use App\Models\BodyWeight;
use Illuminate\Support\Carbon;

it('can be created with factory', function () {
    $bodyWeight = BodyWeight::factory()->create();

    expect($bodyWeight)->toBeInstanceOf(BodyWeight::class)
        ->and($bodyWeight->stones)->toBeInt()
        ->and($bodyWeight->pounds)->toBeString();
});

it('casts recorded_at to date', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'recorded_at' => '2026-01-06',
    ]);

    expect($bodyWeight->recorded_at)->toBeInstanceOf(Carbon::class)
        ->and($bodyWeight->recorded_at->format('Y-m-d'))->toBe('2026-01-06');
});

it('casts stones to integer', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'stones' => 12,
    ]);

    expect($bodyWeight->stones)->toBe(12);
});

it('casts pounds to decimal', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'pounds' => 7.5,
    ]);

    expect($bodyWeight->pounds)->toBe('7.5');
});

it('formats weight correctly', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 7.5,
    ]);

    expect($bodyWeight->formatted_weight)->toBe('12st 7.5lbs');
});

it('calculates total pounds', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 7.0,
    ]);

    // 12 * 14 + 7 = 175
    expect($bodyWeight->total_pounds)->toBe(175.0);
});

it('calculates total kilograms', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'stones' => 12,
        'pounds' => 7.0,
    ]);

    // 175 * 0.453592 = 79.38
    expect($bodyWeight->total_kilograms)->toBe(79.4);
});

it('returns null photo url when no photo', function () {
    $bodyWeight = BodyWeight::factory()->create([
        'photo_path' => null,
    ]);

    expect($bodyWeight->photo_url)->toBeNull();
});

it('can create body weight for today using factory state', function () {
    Carbon::setTestNow('2026-01-06');

    $bodyWeight = BodyWeight::factory()->today()->create();

    expect($bodyWeight->recorded_at->format('Y-m-d'))->toBe('2026-01-06');

    Carbon::setTestNow();
});
