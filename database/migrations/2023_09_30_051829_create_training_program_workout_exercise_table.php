<?php
/**
 * Created by ${USER}.
 * Date: 30.09.2023
 * Time: 05.18
 * Company: Rivera Consulting
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_program_workout_exercise', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('training_program_id');
            $table->bigInteger('workout_exercise_id');
            $table->integer('order');
            $table->string('description');
            $table->string('repetitions');
            $table->integer('sets');
            $table->time('rest');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_program_workout_exercise');
    }
};
