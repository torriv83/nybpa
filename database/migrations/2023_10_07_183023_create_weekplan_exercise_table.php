<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weekplan_exercises', function (Blueprint $table) {
            $table->id();
            $table->integer('day');
            $table->unsignedBigInteger('weekplan_id');
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('training_program_id')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('intensity');
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('weekplan_id')->references('id')->on('weekplans')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekplan_exercises');
    }
};
