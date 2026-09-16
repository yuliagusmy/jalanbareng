<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->foreignId('activation_id')
                ->nullable()
                ->after('user_id')
                ->constrained('activations')
                ->nullOnDelete();
            
            $table->index('activation_id');
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropForeign(['activation_id']);
            $table->dropIndex(['activation_id']);
            $table->dropColumn('activation_id');
        });
    }
};
