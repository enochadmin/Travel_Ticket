<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->enum('flight_type', ['national', 'international'])->default('national')->after('passenger_count');
        });

        DB::statement("ALTER TABLE travel_requests MODIFY COLUMN status ENUM('pending_pm', 'pending_commercial', 'pending_hod', 'pending_ceo', 'approved', 'rejected') DEFAULT 'pending_pm'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE travel_requests MODIFY COLUMN status ENUM('pending_pm', 'pending_commercial', 'pending_hod', 'approved', 'rejected') DEFAULT 'pending_pm'");

        Schema::table('travel_requests', function (Blueprint $table) {
            $table->dropColumn('flight_type');
        });
    }
};
