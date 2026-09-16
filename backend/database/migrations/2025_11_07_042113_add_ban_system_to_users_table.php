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
        Schema::table('users', function (Blueprint $table) {
            // Ban status: null (no ban), 'warning', 'restrict', 'suspend', 'permanent'
            $table->string('ban_status')->nullable()->after('twitter');

            // Ban reason/notes
            $table->text('ban_reason')->nullable()->after('ban_status');

            // Ban start date
            $table->timestamp('banned_at')->nullable()->after('ban_reason');

            // Ban end date (for temporary bans like suspend)
            $table->timestamp('ban_until')->nullable()->after('banned_at');

            // Admin who banned the user
            $table->foreignId('banned_by')->nullable()->after('ban_until')
                ->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['banned_by']);
            $table->dropColumn([
                'ban_status',
                'ban_reason',
                'banned_at',
                'ban_until',
                'banned_by'
            ]);
        });
    }
};
