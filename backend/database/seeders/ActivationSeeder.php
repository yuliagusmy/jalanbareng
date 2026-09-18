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

        // 2. Jalan Bareng Makassar (City Chapter Utama)
        $makassar = Activation::create([
            'name' => 'Jalan Bareng Makassar',
            'slug' => 'jalan-bareng-makassar',
            'category' => 'city',
            'city' => 'Makassar',
            'short_title' => 'Jalan Bareng Makassar',
            'tagline' => 'Ruang berjalan dan berbagi cerita di Kota Daeng',
            'hero_title' => 'JALAN BARENG MAKASSAR',
            'hero_subtitle' => 'Ruang bersama untuk berjalan santai, berbagi cerita, dan mengenal sudut kota Makassar lebih dekat.',
            'hero_image' => 'activations/heroes/hero_makassar.jpg',
            'color_theme' => '#0284C7',
            'description' => 'Jalan Bareng Makassar adalah rumah awal gerakan pejalan kaki dan penjelajah ruang kota. Telah berdenyut selama lebih dari dua tahun, gerakan ini rutin mengajak warga dan kawan perantau menyusuri kawasan kota tua, pecinan, anjungan pesisir, dan lorong-lorong bersejarah kota Makassar setiap akhir pekan.',
            'cta_primary_label' => 'Instagram @jalanbarengmakassar',
            'cta_primary_url' => 'https://instagram.com/jalanbarengmakassar',
            'cta_secondary_label' => 'Lihat Jadwal Jalan',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Koordinator Makassar',
            'contact_phone' => '081234567891',
            'social_instagram' => 'jalanbarengmakassar',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->seedFaqs($makassar->id, [
            ['Q' => 'Kapan jadwal rutin jalan santai di Makassar?', 'A' => 'Jadwal rutin biasanya diadakan setiap akhir pekan (Sabtu atau Minggu pagi pukul 06.00 WITA) dengan rute bergantian antara kawasan heritage, pesisir Pantai Losari, maupun lorong pecinan.'],
            ['Q' => 'Apakah kegiatan ini berbayar?', 'A' => 'Tidak, seluruh agenda rutin jalan santai Jalan Bareng Makassar gratis dan terbuka untuk umum.'],
            ['Q' => 'Apa saja yang perlu dibawa saat ikut jalan?', 'A' => 'Gunakan sepatu jalan yang nyaman, bawa botol minum (tumbler) sendiri, topi atau payung bila cuaca terik, dan siapkan semangat untuk berkenalan dengan kawan baru.'],
        ]);

        $this->seedTestimonials($makassar->id, [
            ['name' => 'Ayu Lestari', 'quote' => 'Ikut Jalan Bareng bikin aku yang perantau jadi hafal sudut-sudut Makassar. Dari Somba Opu sampai Pecinan, suasananya seru dan hangat.'],
            ['name' => 'Budi Santoso', 'quote' => 'Kegiatan yang sangat positif! Selain olahraga jalan kaki, kita juga belajar sejarah bangunan tua langsung dari narasumber lokal.'],
        ]);

        $this->seedMedia($makassar->id, [
            ['file' => 'activations/media/media_walking_2.jpg', 'desc' => 'Walking tour menyusuri kawasan heritage Makassar'],
            ['file' => 'activations/media/media_heritage_1.jpg', 'desc' => 'Eksplorasi bangunan cagar budaya bersama kawan pejalan'],
            ['file' => 'activations/media/media_walking_3.jpg', 'desc' => 'Suasana hangat peserta saat jeda istirahat di trotoar kota'],
        ]);

        // 3. Jalan Bareng Palopo (City Chapter)
        $palopo = Activation::create([
            'name' => 'Jalan Bareng Palopo',
            'slug' => 'jalan-bareng-palopo',
            'category' => 'city',
            'city' => 'Palopo',
            'short_title' => 'Jalan Bareng Palopo',
            'tagline' => 'Berjalan santai dan berbagi cerita di Kota Idaman',
            'hero_title' => 'JALAN BARENG PALOPO',
            'hero_subtitle' => 'Komunitas pejalan kaki Palopo yang suka jalan santai, menikmati udara sejuk, dan merajut keakraban warga.',
            'hero_image' => 'activations/heroes/hero_palopo.jpg',
            'color_theme' => '#16A34A',
            'description' => 'Wadah bagi warga dan pemuda di Kota Palopo untuk berkumpul di akhir pekan, berjalan santai menyusuri ruang-ruang publik Palopo, kawasan pelabuhan Tanjung Ringgit, dan saling bertukar cerita kehidupan kota.',
            'cta_primary_label' => 'Instagram @jalanbarengpalopo',
            'cta_primary_url' => 'https://instagram.com/jalanbarengpalopo',
            'cta_secondary_label' => 'Lihat Agenda',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Admin Palopo',
            'contact_phone' => '081234567892',
            'social_instagram' => 'jalanbarengpalopo',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $this->seedFaqs($palopo->id, [
            ['Q' => 'Di mana titik kumpul rutin di Palopo?', 'A' => 'Titik kumpul biasanya berada di Lapangan Gaspa atau kawasan Pantai Labombo. Pantau informasi rute mingguan di Instagram @jalanbarengpalopo.'],
        ]);

        $this->seedTestimonials($palopo->id, [
            ['name' => 'Faisal Basri', 'quote' => 'Senang akhirnya ada komunitas jalan kaki di Palopo! Ramai dan seru untuk olahraga pagi bareng keluarga.'],
        ]);

        $this->seedMedia($palopo->id, [
            ['file' => 'activations/media/media_walking_4.jpg', 'desc' => 'Jalan pagi menikmati udara segar kota Palopo'],
        ]);

        // 4. Jalan Bareng Kabupaten Gowa (City Chapter)
        $gowa = Activation::create([
            'name' => 'Jalan Bareng Kabupaten Gowa',
            'slug' => 'jalan-bareng-gowa',
            'category' => 'city',
            'city' => 'Gowa',
            'short_title' => 'Jalan Bareng Gowa',
            'tagline' => 'Menelusuri jejak cagar budaya dan sejarah Gowa',
            'hero_title' => 'JALAN BARENG GOWA',
            'hero_subtitle' => 'Jelajah cagar budaya, benteng bersejarah, dan ruang hijau Kabupaten Gowa bersama kawan baru.',
            'hero_image' => 'activations/heroes/hero_gowa.jpg',
            'color_theme' => '#CA8A04',
            'description' => 'Aktivasi Jalan Bareng di Kabupaten Gowa yang memadukan kegiatan jalan kaki sehat dengan edukasi cagar budaya kerajaan Gowa. Menyusuri rute Benteng Somba Opu, Kompleks Makam Katangka, hingga ruang terbuka hijau Sungguminasa.',
            'cta_primary_label' => 'Info Kegiatan Gowa',
            'cta_primary_url' => 'https://instagram.com/jalanbarengind',
            'cta_secondary_label' => 'Lihat Rute',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Admin Regional Gowa',
            'contact_phone' => '081234567893',
            'social_instagram' => 'jalanbarengind',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $this->seedFaqs($gowa->id, [
            ['Q' => 'Apakah rute di Gowa ramah untuk pemula?', 'A' => 'Sangat ramah. Jarak rute rata-rata 3 hingga 4 km dengan tempo santai dan banyak jeda di titik bersejarah.'],
        ]);

        $this->seedMedia($gowa->id, [
            ['file' => 'activations/media/media_heritage_1.jpg', 'desc' => 'Menelusuri situs sejarah dan rumah adat di Somba Opu Gowa'],
        ]);

        // 5. Jalan Bareng Kabupaten Bone (City Chapter)
        $bone = Activation::create([
            'name' => 'Jalan Bareng Kabupaten Bone',
            'slug' => 'jalan-bareng-bone',
            'category' => 'city',
            'city' => 'Bone',
            'short_title' => 'Jalan Bareng Bone',
            'tagline' => 'Menyusuri kearifan lokal dan sudut bersejarah Bumi Arung Palakka',
            'hero_title' => 'JALAN BARENG BONE',
            'hero_subtitle' => 'Berjalan santai menyapa ruang terbuka publik dan cerita budaya tanah Bone.',
            'hero_image' => 'activations/heroes/hero_bone.jpg',
            'color_theme' => '#DC2626',
            'description' => 'Komunitas pejalan kaki di Kabupaten Bone yang mengajak generasi muda untuk aktif bergerak, mengenal peninggalan sejarah lokal di Watampone, dan mempererat silaturahmi antarwarga.',
            'cta_primary_label' => 'Info Kegiatan Bone',
            'cta_primary_url' => 'https://instagram.com/jalanbarengind',
            'cta_secondary_label' => 'Lihat Agenda',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Admin Regional Bone',
            'contact_phone' => '081234567894',
            'social_instagram' => 'jalanbarengind',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $this->seedMedia($bone->id, [
            ['file' => 'activations/media/media_heritage_2.jpg', 'desc' => 'Rute cagar budaya dan ruang terbuka di Bone'],
        ]);

        // 6. Jalan Bareng Kota Jakarta Selatan (City Chapter)
        $jaksel = Activation::create([
            'name' => 'Jalan Bareng Jakarta Selatan',
            'slug' => 'jalan-bareng-jaksel',
            'category' => 'city',
            'city' => 'Jakarta Selatan',
            'short_title' => 'Jalan Bareng Jaksel',
            'tagline' => 'Melambat sejenak di pedestrian dan taman kota ibu kota',
            'hero_title' => 'JALAN BARENG JAKARTA SELATAN',
            'hero_subtitle' => 'Menikmati trotoar ramah pejalan kaki, taman terintegrasi transportasi umum, dan arsitektur urban.',
            'hero_image' => 'activations/heroes/hero_jaksel.jpg',
            'color_theme' => '#4F46E5',
            'description' => 'Chapter Jalan Bareng di Jakarta Selatan yang menghubungkan para komuter, perantau, dan penjelajah kota untuk menikmati ruang pejalan kaki yang asri di kawasan Blok M, Senopati, hingga Tebet Eco Park.',
            'cta_primary_label' => 'Info Kegiatan Jaksel',
            'cta_primary_url' => 'https://instagram.com/jalanbarengind',
            'cta_secondary_label' => 'Lihat Agenda',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Admin Jaksel',
            'contact_phone' => '081234567895',
            'social_instagram' => 'jalanbarengind',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 6,
        ]);

        $this->seedMedia($jaksel->id, [
            ['file' => 'activations/media/media_walking_1.jpg', 'desc' => 'Menyusuri koridor pedestrian ramah pejalan kaki Jakarta Selatan'],
        ]);

        // 7. Makan Bareng (Thematic: Gastronomi & Narasi Sejarah)
        $makan = Activation::create([
            'name' => 'Makan Bareng',
            'slug' => 'makan-bareng',
            'category' => 'theme',
            'city' => 'Makassar',
            'short_title' => 'Makan Bareng',
            'tagline' => 'Makan bersama sambil membahas sejarah dan cerita di baliknya',
            'hero_title' => 'MAKAN BARENG',
            'hero_subtitle' => 'Sebuah komunitas makan bersama dengan mendalami sejarah kuliner, resep legendaris, dan cerita manusia di baliknya.',
            'hero_image' => 'activations/heroes/hero_makan_bareng.jpg',
            'color_theme' => '#EA580C',
            'description' => 'Makan Bareng adalah komunitas eksplorasi gastronomi di bawah naungan Jalan Bareng. Kami berkumpul tidak sekadar untuk mencicipi hidangan khas kota, namun juga menelisik sejarah asal-usul bumbu, filosofi penyajian, dan narasi para peracik makanan legendaris yang telah menjaga warisan rasa kota selama puluhan tahun.',
            'cta_primary_label' => 'Instagram @makanbarengmakassar',
            'cta_primary_url' => 'https://instagram.com/makanbarengmakassar',
            'cta_secondary_label' => 'Agenda Kuliner',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Tim Makan Bareng',
            'contact_phone' => '081234567896',
            'social_instagram' => 'makanbarengmakassar',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 7,
        ]);

        $this->seedFaqs($makan->id, [
            ['Q' => 'Bagaimana sistem pembayaran saat sesi Makan Bareng?', 'A' => 'Sistem pembayaran biasanya berupa patungan (split bill) adil atau paket menu khusus yang telah dikoordinasikan sebelumnya dengan pihak kedai.'],
            ['Q' => 'Tempat makan apa saja yang biasanya dikunjungi?', 'A' => 'Mulai dari kedai coto dan konro bersejarah, warung kopi tertua di kawasan pecinan, hingga jajanan pasar tradisional yang sarat cerita budaya.'],
        ]);

        $this->seedTestimonials($makan->id, [
            ['name' => 'Dian Anggraini', 'quote' => 'Makan jadi punya makna berbeda! Bukan cuma kenyang, tapi jadi ngerti sejarah kenapa kuah coto diracik dengan rempah seperti itu.'],
        ]);

        $this->seedMedia($makan->id, [
            ['file' => 'activations/media/media_culinary_1.jpg', 'desc' => 'Makan bersama di kedai kuliner legendaris'],
            ['file' => 'activations/media/media_culinary_2.jpg', 'desc' => 'Diskusi santai mengenai sejarah rempah dan cerita warisan rasa'],
        ]);

        // 8. Explore Bareng (Thematic: Eksplorasi Kota & Media)
        $explore = Activation::create([
            'name' => 'Explore Bareng',
            'slug' => 'explore-bareng',
            'category' => 'theme',
            'city' => null,
            'short_title' => 'Explore Bareng',
            'tagline' => 'Media aktivitas jalan kaki, eksplorasi kota, dan gathering sosial',
            'hero_title' => 'EXPLORE BARENG',
            'hero_subtitle' => 'Temukan sudut tersembunyi, potret arsitektur kota, dan ragam petualangan urban bersama kawan baru.',
            'hero_image' => 'activations/heroes/hero_explore_bareng.jpg',
            'color_theme' => '#D97706',
            'description' => 'Explore Bareng merupakan media aktivitas dan inisiatif penjelajahan kota yang mendokumentasikan keunikan ruang kota, spot tersembunyi (hidden gems), dinamika pedestrian, serta mengadakan sesi social gathering dan petualangan urban.',
            'cta_primary_label' => 'Instagram @explorebareng__',
            'cta_primary_url' => 'https://instagram.com/explorebareng__',
            'cta_secondary_label' => 'Lihat Dokumentasi',
            'cta_secondary_url' => '#galeri',
            'contact_person' => 'Tim Explore Bareng',
            'contact_phone' => '081234567897',
            'social_instagram' => 'explorebareng__',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 8,
        ]);

        $this->seedFaqs($explore->id, [
            ['Q' => 'Apa bedanya Explore Bareng dengan rute jalan biasa?', 'A' => 'Explore Bareng fokus pada rute eksploratif ke tempat-tempat yang jarang dikunjungi umum, fotografi jalanan (street photography), dan membedah cerita tersembunyi di sudut kota.'],
        ]);

        $this->seedMedia($explore->id, [
            ['file' => 'activations/media/media_walking_3.jpg', 'desc' => 'Eksplorasi lorong-lorong unik dan dinding bernarasi seni'],
            ['file' => 'activations/media/media_heritage_2.jpg', 'desc' => 'Dokumentasi arsitektur sudut kota tempo dulu'],
        ]);

        // 9. Diskusi Buku Bareng (Thematic: Komunitas Literasi)
        $diskusi = Activation::create([
            'name' => 'Diskusi Buku Bareng',
            'slug' => 'diskusi-buku-bareng',
            'category' => 'theme',
            'city' => null,
            'short_title' => 'Diskusi Buku Bareng',
            'tagline' => 'Ruang literasi dan bertukar pikiran santai lewat buku',
            'hero_title' => 'DISKUSI BUKU BARENG',
            'hero_subtitle' => 'Komunitas literasi yang rutin bertemu untuk membahas buku, gagasan segar, dan sudut pandang hidup.',
            'hero_image' => 'activations/heroes/hero_diskusi_buku.jpg',
            'color_theme' => '#7C3AED',
            'description' => 'Diskusi Buku Bareng adalah rumah bagi para pencinta lembaran buku dan wacana reflektif. Kami menyelenggarakan sesi membaca hening (silent reading), bedah buku tematik, dan obrolan hangat tentang literasi kota tanpa pretensi akademis yang kaku.',
            'cta_primary_label' => 'Instagram @diskusibukubareng',
            'cta_primary_url' => 'https://instagram.com/diskusibukubareng',
            'cta_secondary_label' => 'Jadwal Pertemuan',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Tim Diskusi Buku',
            'contact_phone' => '081234567898',
            'social_instagram' => 'diskusibukubareng',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 9,
        ]);

        $this->seedFaqs($diskusi->id, [
            ['Q' => 'Apakah harus membaca buku yang sama?', 'A' => 'Tidak harus. Dalam sesi regular, setiap peserta boleh membawa buku apa saja yang sedang dibaca dan menceritakan intisarinya kepada kawan lain.'],
        ]);

        $this->seedTestimonials($diskusi->id, [
            ['name' => 'Aditya Nugraha', 'quote' => 'Tempat terbaik buat nemu bacaan baru dan dengerin berbagai sudut pandang tanpa ada yang menghakimi.'],
        ]);

        $this->seedMedia($diskusi->id, [
            ['file' => 'activations/media/media_books_1.jpg', 'desc' => 'Lingkaran diskusi buku santai di ruang terbuka'],
            ['file' => 'activations/media/media_books_2.jpg', 'desc' => 'Tukar rekomendasi buku bacaan favorit antarpeserta'],
        ]);

        // 10. Lanjut Bergerak (Thematic: Kerelawanan & Hak Anak)
        $lanjut = Activation::create([
            'name' => 'Lanjut Bergerak',
            'slug' => 'lanjut-bergerak',
            'category' => 'theme',
            'city' => null,
            'short_title' => 'Lanjut Bergerak',
            'tagline' => 'Komunitas kerelawanan sosial yang berfokus pada hak anak',
            'hero_title' => 'LANJUT BERGERAK',
            'hero_subtitle' => 'Aksi nyata kerelawanan, pendampingan belajar, dan ruang gembira bagi tumbuh kembang anak-anak.',
            'hero_image' => 'activations/heroes/hero_lanjut_bergerak.jpg',
            'color_theme' => '#059669',
            'description' => 'Lanjut Bergerak adalah komunitas sosial (volunteering) di bawah Jalan Bareng yang berfokus pada pemenuhan hak anak, pendampingan pendidikan alternatif, dan kepedulian lingkungan sosial. Kami rutin mengadakan kelas belajar kreatif, mendongeng ceria, dan aksi peduli bersama anak-anak di pemukiman kota.',
            'cta_primary_label' => 'Instagram @lanjutbergerak',
            'cta_primary_url' => 'https://instagram.com/lanjutbergerak',
            'cta_secondary_label' => 'Aksi Kerelawanan',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Koordinator Relawan',
            'contact_phone' => '081234567899',
            'social_instagram' => 'lanjutbergerak',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $this->seedFaqs($lanjut->id, [
            ['Q' => 'Bagaimana cara bergabung menjadi relawan Lanjut Bergerak?', 'A' => 'Pendaftaran relawan dibuka secara berkala melalui link pendaftaran di bio Instagram @lanjutbergerak sebelum kegiatan aksi sosial diadakan.'],
        ]);

        $this->seedTestimonials($lanjut->id, [
            ['name' => 'Nadhira Putri', 'quote' => 'Melihat senyum anak-anak waktu diajak menggambar dan mendongeng bareng bener-bener menyentuh hati. Gerakan yang sangat bermakna.'],
        ]);

        $this->seedMedia($lanjut->id, [
            ['file' => 'activations/media/media_social_1.jpg', 'desc' => 'Kegiatan belajar ceria dan dongeng bersama anak-anak'],
            ['file' => 'activations/media/media_social_2.jpg', 'desc' => 'Para relawan Lanjut Bergerak dalam aksi kepedulian sosial'],
        ]);

        // 11. Djari Djemari Makassar (Thematic: Handcrafting & Kriya Tangan)
        $djari = Activation::create([
            'name' => 'Djari Djemari Makassar',
            'slug' => 'djari-djemari-makassar',
            'category' => 'theme',
            'city' => 'Makassar',
            'short_title' => 'Djari Djemari',
            'tagline' => 'Pemberdayaan anggota melalui komunitas handcrafting dan seni kriya',
            'hero_title' => 'DJARI DJEMARI MAKASSAR',
            'hero_subtitle' => 'Wadah berkarya dengan tangan, merajut kreativitas, dan memberdayakan potensi anggota lewat seni kriya.',
            'hero_image' => 'activations/heroes/hero_djari_djemari.jpg',
            'color_theme' => '#DB2777',
            'description' => 'Upaya pemberdayaan anggota komunitas dalam bentuk kerajinan tangan (handcrafting). Djari Djemari mengadakan workshop merajut, macrame, beadwork, clay, hingga pembuatan merchandise komunal yang mendukung kemandirian kreatif anak muda.',
            'cta_primary_label' => 'Instagram @djaridjemarimakassar',
            'cta_primary_url' => 'https://instagram.com/djaridjemarimakassar',
            'cta_secondary_label' => 'Jadwal Workshop',
            'cta_secondary_url' => '#jadwal',
            'contact_person' => 'Tim Djari Djemari',
            'contact_phone' => '081234567801',
            'social_instagram' => 'djaridjemarimakassar',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 11,
        ]);

        $this->seedFaqs($djari->id, [
            ['Q' => 'Apakah pemula yang belum pernah membuat kerajinan tangan bisa ikut?', 'A' => 'Tentu saja! Workshop kami dirancang ramah pemula dengan bimbingan langkah demi langkah serta alat dan bahan yang sudah disediakan.'],
        ]);

        $this->seedTestimonials($djari->id, [
            ['name' => 'Fadhilah Nur', 'quote' => 'Workshop kriya di Djari Djemari seru banget buat healing akhir pekan. Hasil rajutannya bisa dibawa pulang dan dipajang.'],
        ]);

        $this->seedMedia($djari->id, [
            ['file' => 'activations/media/media_craft_1.jpg', 'desc' => 'Sesi workshop membuat kerajinan kriya tangan bersama anggota'],
            ['file' => 'activations/media/media_craft_2.jpg', 'desc' => 'Hasil karya kerajinan tangan kreatif peserta'],
        ]);

        // 12. Jalan Bareng Creative Space (Physical Space & Creative Hub)
        $space = Activation::create([
            'name' => 'Jalan Bareng Creative Space',
            'slug' => 'jalan-bareng-creative-space',
            'category' => 'space',
            'city' => 'Makassar',
            'short_title' => 'Creative Space',
            'tagline' => 'Ruang kreatif komunal untuk berkarya, berkolaborasi, dan berbagi',
            'hero_title' => 'JALAN BARENG CREATIVE SPACE',
            'hero_subtitle' => 'Rumah bersama untuk workshop kriya, diskusi buku, pameran mini, dan inkubasi ide komunitas.',
            'hero_image' => 'activations/heroes/hero_creative_space.jpg',
            'color_theme' => '#E11D48',
            'description' => 'Jalan Bareng Creative Space adalah ruang fisik dan simpul kolaborasi terbuka. Berfungsi sebagai studio workshop, ruang membaca, ruang pamer mini, serta titik kumpul bagi komunitas penjelajah kota sebelum dan sesudah kegiatan berjalan kaki.',
            'cta_primary_label' => 'Instagram @jalanbarengcreativespace',
            'cta_primary_url' => 'https://instagram.com/jalanbarengcreativespace',
            'cta_secondary_label' => 'Kunjungi Space',
            'cta_secondary_url' => 'https://instagram.com/jalanbarengcreativespace',
            'contact_person' => 'Pengelola Creative Space',
            'contact_phone' => '081234567802',
            'social_instagram' => 'jalanbarengcreativespace',
            'social_linktree' => 'https://linktr.ee/jalanbarengspace',
            'show_schedule' => true,
            'show_gallery' => true,
            'show_testimonials' => true,
            'show_faq' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 12,
        ]);

        $this->seedFaqs($space->id, [
            ['Q' => 'Apakah Creative Space terbuka untuk komunitas luar mengadakan acara?', 'A' => 'Ya, kami membuka ruang kolaborasi untuk komunitas lain yang ingin menyelenggarakan workshop, diskusi buku, atau pameran karya.'],
        ]);

        $this->seedTestimonials($space->id, [
            ['name' => 'Rina Wijaya', 'quote' => 'Creative Space ini benar-benar ruang yang inspiratif dan hangat! Enak banget buat kumpul santai atau ngerjain project kreatif bareng kawan.'],
        ]);

        $this->seedMedia($space->id, [
            ['file' => 'activations/media/media_space_1.jpg', 'desc' => 'Ruang komunal Jalan Bareng Creative Space untuk workshop dan diskusi'],
            ['file' => 'activations/media/media_space_2.jpg', 'desc' => 'Pameran mini dan instalasi kreasi komunitas'],
        ]);

        $this->command->info('✅ Seluruh ekosistem Jalan Bareng berhasil di-seed:');
        $this->command->info('   - 12 Aktivasi lengkap (Nasional, 5 Chapter Regional, 5 Aktivasi Tematik, 1 Creative Space)');
        $this->command->info('   - Foto asli, FAQ autentik, dan Testimoni terintegrasi.');
    }

    private function seedFaqs(int $activationId, array $faqs): void
    {
        foreach ($faqs as $i => $item) {
            ActivationFaq::create([
                'activation_id' => $activationId,
                'question' => $item['Q'],
                'answer' => $item['A'],
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function seedTestimonials(int $activationId, array $testimonials): void
    {
        foreach ($testimonials as $i => $item) {
            ActivationTestimonial::create([
                'activation_id' => $activationId,
                'author_name' => $item['name'],
                'quote' => $item['quote'],
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function seedMedia(int $activationId, array $mediaList): void
    {
        foreach ($mediaList as $i => $item) {
            ActivationMedia::create([
                'activation_id' => $activationId,
                'type' => 'photo',
                'file_url' => $item['file'],
                'description' => $item['desc'],
                'sort_order' => $i + 1,
            ]);
        }
    }
}
