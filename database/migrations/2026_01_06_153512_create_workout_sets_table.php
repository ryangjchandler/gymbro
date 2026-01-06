<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('set_number');
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->unsignedInteger('reps')->nullable();
            $table->boolean('is_warmup')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['workout_id', 'exercise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_sets');
    }
};
