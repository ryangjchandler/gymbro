<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->date('scheduled_date');
            $table->foreignId('workout_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('weekly_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('scheduled_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
