<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_code')->nullable()->unique()->after('name');
            $table->string('location')->nullable()->after('project_code');
            $table->string('region')->nullable()->after('location');
            $table->string('discipline')->nullable()->after('region'); // Infrastructure, Water, Building
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete()->after('discipline');
            $table->date('start_date')->nullable()->after('manager_id');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('status')->default('active')->after('end_date'); // active, on-hold, completed, cancelled
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn([
                'project_code',
                'location',
                'region',
                'discipline',
                'manager_id',
                'start_date',
                'end_date',
                'status',
            ]);
        });
    }
};
