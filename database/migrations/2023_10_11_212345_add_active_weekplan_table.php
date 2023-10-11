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
        Schema::table('weekplans', function (Blueprint $table) {
            $table->boolean('is_active')->default(0); // Adding is_active column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekplans', function (Blueprint $table) {
            $table->dropColumn('is_active');  // Dropping is_active column
        });
    }
};
