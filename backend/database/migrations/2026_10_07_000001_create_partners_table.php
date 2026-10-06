<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['brand', 'government', 'bumn', 'community', 'media'])->default('brand');
            $table->string('role')->nullable();                     // misal: "Sponsor Utama", "Media Partner"
            $table->string('collab_type')->nullable();              // misal: "Sponsorship", "MoU", "In-Kind"
            $table->string('initial', 10)->nullable();              // singkatan: "BMW", "BRI" (fallback jika no logo)
            $table->string('logo_url')->nullable();                 // URL logo (bisa CDN / storage)
            $table->string('bg_color', 20)->default('#F3F4F6');     // warna background logo card
            $table->string('text_color', 20)->default('#111827');   // warna teks fallback initial
            $table->string('website_url')->nullable();              // link ke website mitra
            $table->integer('sort_order')->default(0);             // urutan tampil
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
