<?php
/**
 * Created by Tor J. Rivera.
 * Date: 06.11.2023
 * Time: 16.32
 * Company: Rivera Consulting
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wishlist_items', function (Blueprint $table)
        {
            $table->string('status');
        });
    }

    public function down(): void
    {
        Schema::table('wishlist_items', function (Blueprint $table)
        {
            //
        });
    }
};
