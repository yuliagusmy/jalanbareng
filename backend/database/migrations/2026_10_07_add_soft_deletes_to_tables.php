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
        // Add soft deletes to events table
        if (!Schema::hasColumn('events', 'deleted_at')) {
            Schema::table('events', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to destinations table
        if (!Schema::hasColumn('destinations', 'deleted_at')) {
            Schema::table('destinations', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to stories table (should already have it, but check)
        if (!Schema::hasColumn('stories', 'deleted_at')) {
            Schema::table('stories', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
