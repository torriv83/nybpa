<?php
/**
 * Created by ${USER}.
 * Date: 11.04.2023
 * Time: 20.04
 * Company: Rivera Consulting
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekplans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('data');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekplans');
    }
};
