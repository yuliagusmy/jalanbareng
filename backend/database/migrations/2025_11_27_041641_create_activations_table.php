<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activations', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('category', ['city', 'theme', 'space', 'other'])->default('other');
            $table->string('city', 100)->nullable();
            $table->string('short_title', 100)->nullable();
            $table->string('tagline')->nullable();
            
            // Hero Section
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('icon')->nullable();
            $table->string('color_theme', 20)->default('#1976D2');
            
            // Content
            $table->text('description')->nullable();
            
            // Call-to-Action
            $table->string('cta_primary_label', 100)->nullable();
            $table->string('cta_primary_url')->nullable();
            $table->string('cta_secondary_label', 100)->nullable();
            $table->string('cta_secondary_url')->nullable();
            
            // Contact Information
            $table->string('contact_person', 100)->nullable();
            $table->string('contact_phone', 50)->nullable();
            
            // Social Media
            $table->string('social_instagram', 100)->nullable();
            $table->string('social_twitter', 100)->nullable();
            $table->string('social_linktree')->nullable();
            
            // Display Settings
            $table->boolean('show_schedule')->default(true);
            $table->boolean('show_gallery')->default(true);
            $table->boolean('show_testimonials')->default(true);
            $table->boolean('show_faq')->default(true);
            
            // Status
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Additional Settings (JSON)
            $table->json('settings')->nullable();
            
            // Ordering
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('slug');
            $table->index('category');
            $table->index(['is_active', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activations');
    }
};
