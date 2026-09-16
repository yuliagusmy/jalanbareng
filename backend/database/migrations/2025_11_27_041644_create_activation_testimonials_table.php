<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activation_testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activation_id')->constrained('activations')->onDelete('cascade');
            
            // Author Information
            $table->string('author_name', 100);
            $table->string('author_photo')->nullable();
            
            // Testimonial Quote
            $table->text('quote');
            
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
        Schema::dropIfExists('activation_testimonials');
    }
};
