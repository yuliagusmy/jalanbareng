<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, community_admin, member
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Add role_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->default(3)->constrained('roles')->onDelete('cascade');
            $table->string('phone')->nullable()->after('email');
            $table->string('photo')->nullable()->after('phone');
            $table->string('instagram')->nullable()->after('photo');
            $table->string('facebook')->nullable()->after('instagram');
            $table->string('twitter')->nullable()->after('facebook');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'phone', 'photo', 'instagram', 'facebook', 'twitter']);
        });

        Schema::dropIfExists('roles');
    }
};
