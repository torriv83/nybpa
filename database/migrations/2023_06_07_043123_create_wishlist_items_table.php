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
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // Add additional columns if needed
            $table->string('hva');
            $table->integer('koster');
            $table->string('url');
            $table->integer('antall');

            // Define foreign key constraint for the relationship
            $table->foreignId('wishlist_id')->constrained('wishlists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};
