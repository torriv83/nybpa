<?php
/**
 * Created by ${USER}.
 * Date: 20.09.2023
 * Time: 16.51
 * Company: Rivera Consulting
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resepters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('validTo');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resepters');
    }
};
