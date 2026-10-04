<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activation_media', function (Blueprint $table) {
            if (!Schema::hasColumn('activation_media', 'photographer')) {
                $table->string('photographer')->nullable()->after('description');
            }
            if (!Schema::hasColumn('activation_media', 'activity_date')) {
                $table->date('activity_date')->nullable()->after('photographer');
            }
            if (!Schema::hasColumn('activation_media', 'tag')) {
                $table->string('tag')->nullable()->after('activity_date');
            }
            if (!Schema::hasColumn('activation_media', 'is_featured_home')) {
                $table->boolean('is_featured_home')->default(false)->after('tag');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activation_media', function (Blueprint $table) {
            $table->dropColumn(['photographer', 'activity_date', 'tag', 'is_featured_home']);
        });
    }
};
