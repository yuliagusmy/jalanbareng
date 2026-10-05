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
        Schema::table('events', function (Blueprint $table) {
            $table->string('time', 10)->nullable()->after('date');
            $table->string('meeting_point')->nullable()->after('finish_point');
            $table->boolean('is_curated_tikum')->default(false)->after('meeting_point');
            $table->unsignedInteger('price')->default(0)->after('is_curated_tikum');
            $table->string('price_description')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'time',
                'meeting_point',
                'is_curated_tikum',
                'price',
                'price_description',
            ]);
        });
    }
};
