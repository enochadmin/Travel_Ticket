<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, explicitly alter the ENUM definition to include 'pending_commercial'
        DB::statement("ALTER TABLE travel_requests MODIFY COLUMN status ENUM('pending_pm', 'pending_commercial', 'pending_hod', 'approved', 'rejected') DEFAULT 'pending_pm'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE travel_requests MODIFY COLUMN status ENUM('pending_pm', 'pending_hod', 'approved', 'rejected') DEFAULT 'pending_pm'");
    }
};
