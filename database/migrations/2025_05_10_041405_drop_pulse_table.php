<?php
/**
 * Created by Tor J. Rivera.
 * Date: 10.05.2025
 * Time: 04:14
 * Company: Rivera Consulting
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('pulse_entries');
        Schema::dropIfExists('pulse_measurements');
        Schema::dropIfExists('pulse_monitored_logs');
        Schema::dropIfExists('pulse_requests');
        Schema::dropIfExists('pulse_jobs');
        Schema::dropIfExists('pulse_slow_queries');
        Schema::dropIfExists('pulse_tables');
        Schema::dropIfExists('pulse_users');
    }

    public function down(): void
    {
        // Tom, da tabellene ikke skal gjenopprettes
    }
};
