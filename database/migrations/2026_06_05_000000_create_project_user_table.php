<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });

        DB::table('users')
            ->whereNotNull('project_id')
            ->orderBy('id')
            ->select(['id', 'project_id'])
            ->chunk(100, function ($users) {
                $now = now();

                $rows = $users->map(fn($user) => [
                    'project_id' => $user->project_id,
                    'user_id' => $user->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                DB::table('project_user')->insertOrIgnore($rows);
            });

        DB::table('projects')
            ->whereNotNull('manager_id')
            ->orderBy('id')
            ->select(['id', 'manager_id'])
            ->chunk(100, function ($projects) {
                $now = now();

                $rows = $projects->map(fn($project) => [
                    'project_id' => $project->id,
                    'user_id' => $project->manager_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                DB::table('project_user')->insertOrIgnore($rows);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
