<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['walking', 'regular'])->default('regular');
            $table->text('description');
            $table->dateTime('date');
            $table->string('poster');
            $table->string('youtube_link')->nullable();
            $table->string('registration_link')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->decimal('distance', 8, 2)->nullable(); // in kilometers
            $table->integer('estimated_duration')->nullable(); // in minutes
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('participants_count')->default(0);
            $table->timestamps();
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE events ADD COLUMN start_point geography(Point, 4326)');
            DB::statement('ALTER TABLE events ADD COLUMN finish_point geography(Point, 4326)');
            DB::statement('ALTER TABLE events ADD COLUMN route geography(LineString, 4326)');
        } else {
            Schema::table('events', function (Blueprint $table) {
                $table->text('start_point')->nullable();
                $table->text('finish_point')->nullable();
                $table->text('route')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
