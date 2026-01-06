<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_weights', function (Blueprint $table) {
            $table->id();
            $table->date('recorded_at');
            $table->unsignedTinyInteger('stones');
            $table->decimal('pounds', 4, 1);
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();

            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_weights');
    }
};
