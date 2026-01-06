<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_template_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->unsignedInteger('target_sets')->default(3);
            $table->unsignedInteger('target_reps')->nullable();
            $table->unsignedInteger('target_duration_seconds')->nullable();
            $table->unsignedInteger('rest_seconds')->default(45);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_template_exercises');
    }
};
