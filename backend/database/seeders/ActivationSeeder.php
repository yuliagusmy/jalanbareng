<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activation;
use App\Models\ActivationMedia;
use App\Models\ActivationFaq;
use App\Models\ActivationTestimonial;

class ActivationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Jalan Bareng Makassar (City)
        $makassar = Activation::create([
            'name' => 'Jalan Bareng Makassar',
            'slug' => 'jalan-bareng-makassar',
            'category' => 'city',
            'city' => 'Makassar',
            'short_title' => 'Jalan Bareng Makassar',
            'tagline' => 'Ruang berjalan dan berbagi cerita di kota',
            'hero_title' => 'JALAN BARENG MAKASSAR',
            'hero_subtitle' => 'Ruang bersama untuk berjalan santai, berbagi cerita, dan mengenal sudut kota Makassar lebih dekat.',
            'color_theme' => '#1976D2',
            'description' => 'Jalan Bareng Makassar adalah ruang bersama untuk berjalan santai, berbagi cerita, dan mengenal sudut kota Makassar lebih dekat. Kami mengajak semua orang untuk ikut berjalan, berbagi pengalaman, dan menikmati keindahan kota bersama-sama.',
            'cta_primary_label' => 'Gabung Grup WhatsApp',
            'cta_primary_url' => 'https://chat.whatsapp.com/example',
            'cta_secondary_label' => 'Lihat Jadwal',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Admin Jalan Bareng',
            'contact_phone' => '081234567890',
            'social_instagram' => 'jalanbarengmks',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // FAQs for Makassar
        ActivationFaq::create([
            'activation_id' => $makassar->id,
            'question' => 'Apakah ini berbayar?',
            'answer' => 'Tidak, semua kegiatan Jalan Bareng Makassar gratis dan terbuka untuk umum.',
            'sort_order' => 1,
        ]);

        ActivationFaq::create([
            'activation_id' => $makassar->id,
            'question' => 'Apakah perlu daftar?',
            'answer' => 'Ya, untuk memudahkan koordinasi, silakan daftar melalui link pendaftaran atau bergabung di grup WhatsApp.',
            'sort_order' => 2,
        ]);

        ActivationFaq::create([
            'activation_id' => $makassar->id,
            'question' => 'Siapa saja yang bisa ikut?',
            'answer' => 'Semua orang! Jalan Bareng terbuka untuk semua kalangan, ramah pemula, dan tidak ada batasan usia.',
            'sort_order' => 3,
        ]);

        // Testimonials for Makassar
        ActivationTestimonial::create([
            'activation_id' => $makassar->id,
            'author_name' => 'Ayu Lestari',
            'quote' => 'Ikut Jalan Bareng bikin aku lebih kenal Kota Makassar! Senang bisa ketemu teman-teman baru yang punya hobi sama.',
            'sort_order' => 1,
        ]);

        ActivationTestimonial::create([
            'activation_id' => $makassar->id,
            'author_name' => 'Budi Santoso',
            'quote' => 'Kegiatan yang sangat positif! Selain olahraga, juga bisa jalan-jalan sambil belajar sejarah kota.',
            'sort_order' => 2,
        ]);

        // Jalan Bareng Palopo (City)
        $palopo = Activation::create([
            'name' => 'Jalan Bareng Palopo',
            'slug' => 'jalan-bareng-palopo',
            'category' => 'city',
            'city' => 'Palopo',
            'short_title' => 'Jalan Bareng Palopo',
            'tagline' => 'Berjalan dan berbagi di Kota Palopo',
            'hero_title' => 'JALAN BARENG PALOPO',
            'hero_subtitle' => 'Komunitas pejalan kaki Palopo yang suka jalan santai dan berbagi cerita.',
            'color_theme' => '#4CAF50',
            'description' => 'Jalan Bareng Palopo adalah wadah bagi warga Palopo untuk berkumpul, berjalan santai, dan berbagi cerita tentang kota tercinta.',
            'cta_primary_label' => 'Gabung Grup WhatsApp',
            'cta_primary_url' => 'https://chat.whatsapp.com/example-palopo',
            'cta_secondary_label' => 'Lihat Jadwal',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Admin Palopo',
            'contact_phone' => '081234567891',
            'social_instagram' => 'jalanbarengpalopo',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        ActivationFaq::create([
            'activation_id' => $palopo->id,
            'question' => 'Kapan jadwal rutin Jalan Bareng Palopo?',
            'answer' => 'Biasanya setiap Minggu pagi jam 06:00 WITA. Cek grup WhatsApp untuk info terbaru.',
            'sort_order' => 1,
        ]);

        // Explore Bareng (Theme)
        $explore = Activation::create([
            'name' => 'Explore Bareng',
            'slug' => 'explore-bareng',
            'category' => 'theme',
            'short_title' => 'Explore Bareng',
            'tagline' => 'Eksplorasi ruang kota bersama',
            'hero_title' => 'EXPLORE BARENG',
            'hero_subtitle' => 'Jelajahi sudut-sudut tersembunyi kota, temukan cerita baru, dan nikmati petualangan urban bersama.',
            'color_theme' => '#FF9800',
            'description' => 'Explore Bareng adalah program eksplorasi kota yang mengajak peserta untuk menemukan tempat-tempat unik, spot tersembunyi, dan cerita menarik di berbagai sudut kota.',
            'cta_primary_label' => 'Ikut Eksplorasi',
            'cta_primary_url' => 'https://forms.google.com/example',
            'cta_secondary_label' => 'Lihat Event',
            'cta_secondary_url' => '#events',
            'contact_person' => 'Tim Explore',
            'contact_phone' => '081234567892',
            'social_instagram' => 'explorebareng',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        ActivationFaq::create([
            'activation_id' => $explore->id,
            'question' => 'Apa bedanya Explore Bareng dengan Jalan Bareng biasa?',
            'answer' => 'Explore Bareng fokus pada eksplorasi tempat-tempat unik dan tersembunyi, dengan rute yang lebih variatif dan petualangan yang lebih seru.',
            'sort_order' => 1,
        ]);

        // Diskusi Buku Bareng (Theme)
        $diskusi = Activation::create([
            'name' => 'Diskusi Buku Bareng',
            'slug' => 'diskusi-buku-bareng',
            'category' => 'theme',
            'short_title' => 'Diskusi Buku Bareng',
            'tagline' => 'Ngobrol lewat buku',
            'hero_title' => 'DISKUSI BUKU BARENG',
            'hero_subtitle' => 'Ruang berbagi pemikiran dan diskusi tentang buku, literasi, dan ide-ide menarik.',
            'color_theme' => '#9C27B0',
            'description' => 'Diskusi Buku Bareng adalah komunitas pembaca yang rutin bertemu untuk membahas buku, berbagi pemikiran, dan memperluas wawasan bersama.',
            'cta_primary_label' => 'Gabung Komunitas',
            'cta_primary_url' => 'https://chat.whatsapp.com/diskusi-buku',
            'cta_secondary_label' => 'Lihat Jadwal Diskusi',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Tim Literasi',
            'contact_phone' => '081234567893',
            'social_instagram' => 'diskusibukubareng',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Jalan Bareng Creative Space (Space)
        $creative = Activation::create([
            'name' => 'Jalan Bareng Creative Space',
            'slug' => 'jalan-bareng-creative-space',
            'category' => 'space',
            'short_title' => 'Creative Space',
            'tagline' => 'Ruang kreatif komunal',
            'hero_title' => 'JALAN BARENG CREATIVE SPACE',
            'hero_subtitle' => 'Ruang kolaborasi kreatif untuk berkarya, belajar, dan berbagi bersama komunitas.',
            'color_theme' => '#E91E63',
            'description' => 'Jalan Bareng Creative Space adalah ruang fisik dan komunitas yang menyediakan tempat untuk berkarya, workshop, dan kolaborasi kreatif.',
            'cta_primary_label' => 'Kunjungi Space',
            'cta_primary_url' => 'https://maps.google.com/example',
            'cta_secondary_label' => 'Lihat Program',
            'cta_secondary_url' => '#program',
            'contact_person' => 'Space Manager',
            'contact_phone' => '081234567894',
            'social_instagram' => 'jalanbarengspace',
            'social_linktree' => 'https://linktr.ee/jalanbarengspace',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        ActivationFaq::create([
            'activation_id' => $creative->id,
            'question' => 'Apakah bisa sewa ruangan untuk acara?',
            'answer' => 'Ya, Creative Space bisa disewa untuk workshop, diskusi, atau acara komunitas. Hubungi kami untuk info lebih lanjut.',
            'sort_order' => 1,
        ]);

        ActivationTestimonial::create([
            'activation_id' => $creative->id,
            'author_name' => 'Rina Wijaya',
            'quote' => 'Creative Space ini benar-benar ruang yang inspiratif! Cocok banget buat kerja bareng atau workshop.',
            'sort_order' => 1,
        ]);

        $this->command->info('✅ Activations seeded successfully!');
        $this->command->info('   - 5 activations created');
        $this->command->info('   - 6 FAQs created');
        $this->command->info('   - 3 testimonials created');
    }
}
