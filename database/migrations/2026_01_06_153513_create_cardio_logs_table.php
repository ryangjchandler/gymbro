<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cardio_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('duration_seconds');
            $table->decimal('speed', 4, 1)->nullable();
            $table->decimal('distance_km', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['workout_id', 'exercise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cardio_logs');
    }
};
