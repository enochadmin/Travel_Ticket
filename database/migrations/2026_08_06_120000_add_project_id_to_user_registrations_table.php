<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_registrations', function (Blueprint $table) {
            // Nullable: legacy registrations and "Other" (custom) project entries have no project link.
            $table->foreignId('project_id')->nullable()->after('project_name')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_registrations', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
