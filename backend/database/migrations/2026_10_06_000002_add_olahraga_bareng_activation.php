<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Activation;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Activation::firstOrCreate(
            ['slug' => 'olahraga-bareng'],
            [
                'name' => 'Olahraga Bareng',
                'category' => 'theme',
                'city' => 'Makassar',
                'short_title' => 'Olahraga Bareng',
                'tagline' => 'Keringat bareng, seru-seruan bareng di aneka cabang olahraga',
                'hero_title' => 'OLAHRAGA BARENG',
                'hero_subtitle' => 'Wadah kumpul seru untuk main bulutangkis, golf, padel, tenis, dan ragam olahraga komunal bersama kawan baru.',
                'hero_image' => 'activations/heroes/hero_olahraga_bareng.jpg',
                'color_theme' => '#16A34A',
                'description' => 'Olahraga Bareng adalah inisiatif dan aktivasi tematik di bawah Jalan Bareng untuk menyalurkan energi dan hobi berolahraga secara santai dan komunal. Dari sesi Tiba-Tiba Bultang (bulutangkis), padel, golf, hingga cabang olahraga lainnya. Terbuka bagi siapa saja tanpa memandang tingkat kemahiran, yang penting seru dan sehat bareng.',
                'cta_primary_label' => 'Instagram @jalanbarengind',
                'cta_primary_url' => 'https://instagram.com/jalanbarengind',
                'cta_secondary_label' => 'Lihat Jadwal Olahraga',
                'cta_secondary_url' => '#jadwal',
                'contact_person' => 'Tim Olahraga Bareng',
                'contact_phone' => '081234567813',
                'social_instagram' => 'jalanbarengind',
                'show_schedule' => true,
                'show_gallery' => true,
                'show_testimonials' => true,
                'show_faq' => true,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 13,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Activation::where('slug', 'olahraga-bareng')->delete();
    }
};
