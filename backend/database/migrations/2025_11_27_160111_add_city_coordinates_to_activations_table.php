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
        Schema::table('activations', function (Blueprint $table) {
            $table->decimal('city_latitude', 10, 7)->nullable()->after('city');
            $table->decimal('city_longitude', 10, 7)->nullable()->after('city_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activations', function (Blueprint $table) {
            $table->dropColumn(['city_latitude', 'city_longitude']);
        });
    }
};
