<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_registrations', function (Blueprint $table) {
            // Nullable so registrations submitted before this feature still work.
            $table->string('password')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('user_registrations', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
