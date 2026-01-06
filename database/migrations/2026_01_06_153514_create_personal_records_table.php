<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->decimal('value', 10, 2);
            $table->foreignId('workout_set_id')->nullable()->constrained()->nullOnDelete();
            $table->date('achieved_at');
            $table->timestamps();

            $table->index(['exercise_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_records');
    }
};
