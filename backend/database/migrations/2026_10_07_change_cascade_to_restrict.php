<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tujuan: Mencegah data loss karena cascade delete.
     * Sebelum: DELETE user → semua event/destination user ikut terhapus
     * Sesudah: DELETE user → ERROR (harus hapus event/destination dulu manual)
     */
    public function up(): void
    {
        // Deteksi database driver
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite tidak support DROP FOREIGN KEY
            // Kita skip untuk development (SQLite)
            // Production (MySQL/PostgreSQL) yang akan di-fix
            return;
        }

        // MySQL / PostgreSQL: Drop dan recreate foreign key dengan RESTRICT
        
        // 1. Events table - user_id
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict'); // ❌ Tidak bisa hapus user jika ada event
        });

        // 2. Destinations table - user_id
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('destinations', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict'); // ❌ Tidak bisa hapus user jika ada destination
        });

        // 3. Destinations table - category_id (set null instead of cascade)
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
        
        // Make category_id nullable first
        DB::statement('ALTER TABLE destinations MODIFY category_id BIGINT UNSIGNED NULL');
        
        Schema::table('destinations', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null'); // ✅ Hapus kategori, destination tetap ada (category_id = NULL)
        });

        // 4. Comments table - user_id (set null instead of cascade)
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        DB::statement('ALTER TABLE comments MODIFY user_id BIGINT UNSIGNED NULL');
        
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // ✅ User dihapus, comment tetap ada (user_id = NULL)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        // Kembalikan ke CASCADE (rollback)
        
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('destinations', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
        
        DB::statement('ALTER TABLE destinations MODIFY category_id BIGINT UNSIGNED NOT NULL');
        
        Schema::table('destinations', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        DB::statement('ALTER TABLE comments MODIFY user_id BIGINT UNSIGNED NOT NULL');
        
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
