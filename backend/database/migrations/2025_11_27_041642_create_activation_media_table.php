<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activation_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activation_id')->constrained('activations')->onDelete('cascade');
            
            // Media Type: photo, video, youtube
            $table->enum('type', ['photo', 'video', 'youtube'])->default('photo');
            
            // File URL for uploaded files
            $table->string('file_url')->nullable();
            
            // External URL for YouTube or external videos
            $table->string('external_url')->nullable();
            
            // Description/Caption
            $table->text('description')->nullable();
            
            // Ordering
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('activation_id');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activation_media');
    }
};
