<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('destination'); // Country
            $table->date('travel_date');
            $table->date('return_date')->nullable();
            $table->string('purpose'); // Reason
            $table->enum('status', ['pending_pm', 'pending_hod', 'approved', 'rejected'])->default('pending_pm');
            $table->foreignId('pm_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('hod_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_requests');
    }
};
