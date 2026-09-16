<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Seeder;

class StorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@jalanbareng.com')->first();
        $userId = $admin ? $admin->id : null;

        $stories = [
            [
                'title' => 'Learning Report: Membangun Ruang Aman & Nyaman Bagi Pejalan Kaki Pemula',
                'slug' => 'learning-report-membangun-ruang-aman-pejalan-kaki-pemula',
                'excerpt' => 'Riset dan refleksi dari perjalanan 100+ km menyusuri trotoar, gang sempit, dan ruang publik ramah komunitas di tiga kota besar.',
                'content' => '<p>Perjalanan menyusuri kota bukan sekadar memindahkan raga dari titik A ke titik B. Bagi komunitas pejalan kaki dan penjelajah lokal di <strong>Jalan Bareng</strong>, setiap langkah adalah medium untuk membaca denyut kehidupan kota yang kerap luput dari balik kaca kendaraan.</p><h3>Menemukan Kembali Ruang Publik</h3><p>Dalam kurun waktu tiga bulan terakhir, tim kolaborator bersama teman-teman komunitas telah mengumpulkan catatan lapangan mengenai aksesibilitas trotoar, titik peristirahatan teduh, serta interaksi sosial warga sekitar lorong perkotaan.</p><p>Hasil observasi menunjukkan bahwa keberadaan vegetasi peneduh dan pencahayaan yang cukup di malam hari meningkatkan rasa aman pejalan kaki hingga 75%. Lewat inisiatif rute bersama, kita membuktikan bahwa warga kota mendambakan ruang bersama yang inklusif, sehat, dan hangat.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?w=800&h=1000&fit=crop',
                'author_name' => 'Tim Riset Jalan Bareng',
                'author_email' => 'riset@jalanbareng.com',
                'author_bio' => 'Divisi riset dan advokasi ruang ramah pejalan kaki.',
                'author_instagram' => '@jalanbareng_id',
                'card_style' => 'coral',
                'status' => 'approved',
                'is_featured' => true,
                'published_at' => now()->subDays(2),
                'views_count' => 142,
            ],
            [
                'title' => 'Makassar City: Menjelajah Ruang Ramah Disabilitas dan Cerita Inklusi Warga',
                'slug' => 'makassar-city-menjelajah-ruang-ramah-disabilitas-cerita-inklusi',
                'excerpt' => 'Menyusuri pesisir Losari hingga gang-gang perkampungan tua untuk menguji sejauh mana kota merangkul seluruh warganya.',
                'content' => '<p>Makassar dikenal dengan angin sepoi pantainya yang khas dan aroma pisang epe yang semerbak. Namun di balik keindahan panorama Pantai Losari, bagaimana kenyamanan kota ini bagi kawan-kawan difabel dan lansia yang ingin menikmati sore hari?</p><h3>Jalan Bareng Inklusif</h3><p>Bersama komunitas kawan dengar dan teman netra lokal, kami menyusuri jalur pedestrian sepanjang 3 kilometer. Kami mencatat kebutuhan jalur pemandu (*guiding block*) yang tidak terputus dan ramp kursi roda yang landai.</p><p>Cerita ini adalah pengingat bahwa kota yang ramah bagi kelompok rentan adalah kota yang nyaman dan manusiawi untuk dinikmati oleh siapa saja.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=800&h=1000&fit=crop',
                'author_name' => 'Siti Nurhaliza',
                'author_email' => 'siti.nurhaliza@jalanbareng.com',
                'author_bio' => 'Koordinator Komunitas Jalan Bareng Makassar.',
                'author_instagram' => '@sitinurhaliza_jb',
                'card_style' => 'magenta',
                'status' => 'approved',
                'is_featured' => true,
                'published_at' => now()->subDays(5),
                'views_count' => 98,
            ],
            [
                'title' => 'Yang Tinggal Bersama Hawa Panas: Cerita Lorong dan Iklim Kota yang Berubah',
                'slug' => 'yang-tinggal-bersama-hawa-panas-cerita-lorong-dan-iklim-kota',
                'excerpt' => 'Di tengah terik aspal kota tropis, lorong-lorong hijau swadaya warga menjadi oase penyejuk yang menyatukan tetangga.',
                'content' => '<p>Suhu perkotaan yang semakin meningkat belakangan ini bukan lagi sekadar angka di ramalan cuaca smartphone. Warga yang beraktivitas kaki dan bersepeda merasakan langsung dampaknya di sekujur kulit.</p><h3>Gotong Royong Tanaman Lorong</h3><p>Ketika menyusuri salah satu perkampungan padat di pinggiran rel kereta, kami disambut oleh jajaran pot lidah mertua, sirih gading, dan pohon mangga rimbun yang ditata apik oleh warga. Lorong tersebut terasa 3-4 derajat lebih sejuk dibandingkan jalan protokol di luarnya.</p><p>Gerakan kecil warga menanam di depan teras bukan hanya sekadar estetika, melainkan mitigasi iklim mikro yang nyata dan patut diduplikasi ke sudut-sudut kota lainnya.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&h=1000&fit=crop',
                'author_name' => 'Fajar Pratama',
                'author_email' => 'fajar.pratama@gmail.com',
                'author_bio' => 'Penggiat urban farming dan anggota aktif Jalan Bareng.',
                'author_instagram' => '@fajar_walks',
                'card_style' => 'amber',
                'status' => 'approved',
                'is_featured' => false,
                'published_at' => now()->subDays(8),
                'views_count' => 76,
            ],
            [
                'title' => 'Merekam Denyut Pejalan Kaki: Catatan dari Sudut Tersembunyi Kota Tua',
                'slug' => 'merekam-denyut-pejalan-kaki-catatan-dari-sudut-tersembunyi-kota-tua',
                'excerpt' => 'Menyusuri jejak bangunan kolonial yang bersanding dengan warung kopi paman legendaris dan obrolan pagi para perajin.',
                'content' => '<p>Pukul enam pagi adalah waktu terbaik untuk menikmati Kota Tua sebelum bising deru knalpot memenuhi jalanan. Dinding bata terkelupas dan jendela kayu raksasa menjadi latar cerita para perajin kunci dan penjual lontong sayur yang sudah berjualan lintas generasi.</p><h3>Menjaga Memori Kolektif</h3><p>Berjalan kaki memberi kita kemewahan untuk melambat dan menyapa. Banyak sudut bersejarah yang tidak tercatat di brosur pariwisata resmi, namun hidup dalam ingatan warga sepuh yang setia menjaga sudut kotanya.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&h=1000&fit=crop',
                'author_name' => 'Budi Santoso',
                'author_email' => 'budi.santoso@gmail.com',
                'author_bio' => 'Penjelajah sejarah lokal dan fotografer jalanan.',
                'author_instagram' => '@budisantoso_street',
                'card_style' => 'photo',
                'status' => 'approved',
                'is_featured' => false,
                'published_at' => now()->subDays(12),
                'views_count' => 110,
            ],
            [
                'title' => 'Menelusuri Rute Kuliner Kaki Lima Tengah Malam di Sudut Stasiun',
                'slug' => 'menelusuri-rute-kuliner-kaki-lima-tengah-malam-sudut-stasiun',
                'excerpt' => 'Mencicipi wedang ronde hangat dan nasi kucing sembari menyimak obrolan para komuter malam yang baru pulang kerja.',
                'content' => '<p>Ini adalah kiriman tulisan dari anggota baru kami yang ingin membagikan rute jalan santai malam hari sembari menjelajah warung-warung makan tradisional di sekitar stasiun kereta.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=1000&fit=crop',
                'author_name' => 'Rina Amelia',
                'author_email' => 'rina.amelia@gmail.com',
                'author_bio' => 'Pecinta kuliner malam dan jalan santai.',
                'author_instagram' => '@rina_kuliner',
                'card_style' => 'coral',
                'status' => 'pending', // Pending curation!
                'is_featured' => false,
                'published_at' => null,
                'views_count' => 0,
            ]
        ];

        foreach ($stories as $storyData) {
            Story::updateOrCreate(
                ['slug' => $storyData['slug']],
                array_merge($storyData, ['user_id' => $userId])
            );
        }

        $this->command->info('StorySeeder executed: 4 approved stories + 1 pending story for curation testing!');
    }
}
