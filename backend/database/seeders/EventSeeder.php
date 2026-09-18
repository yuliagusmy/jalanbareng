<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventPhoto;
use App\Models\User;
use App\Models\Activation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $communityAdmins = User::whereHas('role', fn($q) => $q->where('name', 'community_admin'))->get();

        if ($communityAdmins->isEmpty()) {
            $this->command->warn('No community admins found. Please run UserSeeder first.');
            return;
        }

        // Fetch activations to associate events accurately
        $activations = Activation::all()->keyBy('slug');

        $events = [
            // 1. Jelajah Kota Tua Makassar (Jalan Bareng Makassar)
            [
                'activation_slug' => 'jalan-bareng-makassar',
                'name' => 'Jelajah Kota Tua Makassar: Dari Pantai Losari ke Fort Rotterdam',
                'type' => 'walking',
                'description' => '<p>Mari jelajahi sejarah Kota Makassar melalui jalan santai menyusuri kawasan kota tua. Kita akan mengunjungi Fort Rotterdam, kawasan pecinan, dan bangunan bersejarah lainnya sambil mendengarkan narasi tata kota.</p><p><strong>Rute:</strong> Pantai Losari - Fort Rotterdam - Kawasan Somba Opu - Karebosi</p><p><strong>Yang perlu dibawa:</strong> Sepatu nyaman, tumbler air minum, topi, dan kamera.</p>',
                'date' => Carbon::now()->addDays(7)->setHour(6)->setMinute(0),
                'poster' => 'events/media_walking_1.jpg',
                'registration_link' => 'https://instagram.com/jalanbarengmakassar',
                'youtube_link' => null,
                'start_point' => 'POINT(119.4040 -5.1477)',
                'finish_point' => 'POINT(119.4165 -5.1420)',
                'route' => 'LINESTRING(119.4040 -5.1477, 119.4067 -5.1348, 119.4184 -5.1371, 119.4165 -5.1420)',
                'distance' => 5.2,
                'estimated_duration' => 120,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_walking_2.jpg', 'events/media_heritage_1.jpg'],
            ],

            // 2. Morning Walk: Pantai Losari ke Tanjung Bunga (Jalan Bareng Makassar)
            [
                'activation_slug' => 'jalan-bareng-makassar',
                'name' => 'Morning Walk: Pesisir Losari ke Tanjung Bunga',
                'type' => 'walking',
                'description' => '<p>Jalan santai pagi menyusuri pesisir pantai Makassar. Nikmati hembusan angin laut dan udara segar sambil berolahraga ringan bersama kawan-kawan baru.</p><p><strong>Benefit:</strong> Langkah sehat, relasi hangat, dokumentasi foto estetik!</p><p><strong>Titik Kumpul:</strong> Depan Anjungan Pantai Losari (06.00 WITA)</p>',
                'date' => Carbon::now()->addDays(14)->setHour(5)->setMinute(30),
                'poster' => 'events/media_walking_2.jpg',
                'registration_link' => 'https://instagram.com/jalanbarengmakassar',
                'youtube_link' => null,
                'start_point' => 'POINT(119.4040 -5.1477)',
                'finish_point' => 'POINT(119.4020 -5.1700)',
                'route' => 'LINESTRING(119.4040 -5.1477, 119.4055 -5.1465, 119.4075 -5.1500, 119.4050 -5.1600, 119.4020 -5.1700)',
                'distance' => 4.5,
                'estimated_duration' => 90,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_walking_3.jpg'],
            ],

            // 3. Heritage Walk: Somba Opu Trail (Jalan Bareng Gowa)
            [
                'activation_slug' => 'jalan-bareng-gowa',
                'name' => 'Heritage Walk: Menelusuri Benteng Somba Opu Gowa',
                'type' => 'walking',
                'description' => '<p>Petualangan sejarah menelusuri cagar budaya Benteng Somba Opu. Kita akan belajar tentang kejayaan maritim kerajaan Gowa dan mengamati arsitektur rumah adat Sulawesi Selatan.</p><p><strong>Pemandu:</strong> Pengkaji sejarah lokal</p><p><strong>Biaya:</strong> Gratis dan terbuka untuk umum.</p>',
                'date' => Carbon::now()->addDays(21)->setHour(7)->setMinute(0),
                'poster' => 'events/media_heritage_1.jpg',
                'registration_link' => 'https://instagram.com/jalanbarengind',
                'youtube_link' => null,
                'start_point' => 'POINT(119.3957 -5.1978)',
                'finish_point' => 'POINT(119.3957 -5.1978)',
                'route' => 'LINESTRING(119.3957 -5.1978, 119.3970 -5.1990, 119.3940 -5.1890, 119.3950 -5.1960, 119.3957 -5.1978)',
                'distance' => 3.5,
                'estimated_duration' => 150,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_heritage_2.jpg'],
            ],

            // 4. Makan Bareng: Menelisik Narasi Rasa (Makan Bareng)
            [
                'activation_slug' => 'makan-bareng',
                'name' => 'Makan Bareng: Narasi Rasa & Sejarah di Kedai Legendaris',
                'type' => 'regular',
                'description' => '<p>Sesi kumpul dan santap bersama sambil mendalami narasi budaya di balik kuliner khas kota. Kita akan berdialog dengan pemilik resep turun-temurun dan membedah perpaduan rempah khas Makassar.</p><p><strong>Menu pembuka:</strong> Coto Makassar, Konro, Es Pisang Ijo</p><p><strong>Konsep:</strong> Sharing session & split bill ramah kantong.</p>',
                'date' => Carbon::now()->addDays(10)->setHour(18)->setMinute(30),
                'poster' => 'events/media_culinary_1.jpg',
                'registration_link' => 'https://instagram.com/makanbarengmakassar',
                'youtube_link' => null,
                'start_point' => null,
                'finish_point' => null,
                'route' => null,
                'distance' => null,
                'estimated_duration' => 120,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_culinary_2.jpg'],
            ],

            // 5. Diskusi Buku (Baca Bareng)
            [
                'activation_slug' => 'baca-bareng',
                'name' => 'Baca Bareng: Membaca Ruang Kota & Memori Kolektif',
                'type' => 'regular',
                'description' => '<p>Sesi ngobrol buku santai di ruang terbuka hijau. Peserta dipersilakan membawa buku apa pun yang sedang dibaca bertema kota, perjalanan, atau catatan harian untuk dibagikan secara hangat.</p><p><strong>Format:</strong> 20 menit silent reading + diskusi lingkaran.</p>',
                'date' => Carbon::now()->addDays(12)->setHour(16)->setMinute(0),
                'poster' => 'events/media_books_1.jpg',
                'registration_link' => 'https://instagram.com/jalanbarengind',
                'youtube_link' => null,
                'start_point' => null,
                'finish_point' => null,
                'route' => null,
                'distance' => null,
                'estimated_duration' => 120,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_books_2.jpg'],
            ],

            // 6. Workshop Kriya (Djari Djemari)
            [
                'activation_slug' => 'djari-djemari-makassar',
                'name' => 'Workshop Handcrafting: Seni Merajut Kriya & Macrame',
                'type' => 'regular',
                'description' => '<p>Belajar membuat gantungan kunci macrame dan pouch rajut bersama tim Djari Djemari Makassar. Kelas ramah pemula, seluruh perlengkapan dan bahan disediakan di tempat.</p><p><strong>Fasilitator:</strong> Pengrajin komunitas Djari Djemari</p>',
                'date' => Carbon::now()->addDays(16)->setHour(14)->setMinute(0),
                'poster' => 'events/media_craft_1.jpg',
                'registration_link' => 'https://instagram.com/djaridjemarimakassar',
                'youtube_link' => null,
                'start_point' => null,
                'finish_point' => null,
                'route' => null,
                'distance' => null,
                'estimated_duration' => 180,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_craft_2.jpg'],
            ],

            // 7. Aksi Sosial (Lanjut Bergerak)
            [
                'activation_slug' => 'lanjut-bergerak',
                'name' => 'Ruang Gembira: Kelas Menggambar & Dongeng Bersama Anak Pesisir',
                'type' => 'regular',
                'description' => '<p>Aksi kerelawanan sosial bersama komunitas Lanjut Bergerak. Kami mengajak teman-teman volunteer untuk mendampingi anak-anak belajar mewarnai, mendengarkan dongeng, dan menyalurkan buku bacaan edukatif.</p><p><strong>Kebutuhan:</strong> Volunteer pendamping & donasi buku anak.</p>',
                'date' => Carbon::now()->addDays(18)->setHour(9)->setMinute(0),
                'poster' => 'events/media_social_1.jpg',
                'registration_link' => 'https://instagram.com/lanjutbergerak',
                'youtube_link' => null,
                'start_point' => null,
                'finish_point' => null,
                'route' => null,
                'distance' => null,
                'estimated_duration' => 180,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_social_2.jpg'],
            ],

            // 8. Walking Tour Palopo (Jalan Bareng Palopo)
            [
                'activation_slug' => 'jalan-bareng-palopo',
                'name' => 'Jalan Santai Pagi: Menikmati Semilir Tanjung Ringgit Palopo',
                'type' => 'walking',
                'description' => '<p>Agenda jalan santai pekanan warga Palopo menyusuri kawasan pelabuhan Tanjung Ringgit hingga pusat kota. Rute datar dan ramah untuk segala usia dengan panorama Teluk Bone yang menawan.</p><p><strong>Titik Kumpul:</strong> Dermaga Tanjung Ringgit Palopo (06.00 WITA)</p>',
                'date' => Carbon::now()->addDays(9)->setHour(6)->setMinute(0),
                'poster' => 'events/media_walking_4.jpg',
                'registration_link' => 'https://instagram.com/jalanbarengpalopo',
                'youtube_link' => null,
                'start_point' => 'POINT(120.2030 -3.0030)',
                'finish_point' => 'POINT(120.2080 -3.0010)',
                'route' => 'LINESTRING(120.2030 -3.0030, 120.2050 -3.0020, 120.2080 -3.0010)',
                'distance' => 3.8,
                'estimated_duration' => 90,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_walking_1.jpg'],
            ],

            // 9. JAKARTA SELATAN: Night Walk Blok M (Jalan Bareng Jaksel)
            [
                'activation_slug' => 'jalan-bareng-jaksel',
                'name' => 'City Night Walk: Menyusuri Pedestrian Blok M ke Barito',
                'type' => 'walking',
                'description' => '<p>Menikmati suasana malam Jakarta Selatan yang sejuk dengan berjalan kaki di trotoar ramah pedestrian dari Stasiun MRT Blok M, menembus Taman Literasi, hingga jejeran kios bunga dan kuliner Barito.</p><p><strong>Titik Kumpul:</strong> Plaza Taman Literasi Blok M (19.00 WIB)</p><p><strong>Fasilitas:</strong> Akses mudah transportasi MRT dan TransJakarta.</p>',
                'date' => Carbon::now()->addDays(8)->setHour(19)->setMinute(0),
                'poster' => 'events/media_walking_1.jpg',
                'registration_link' => 'https://instagram.com/jalanbarengind',
                'youtube_link' => null,
                'start_point' => 'POINT(106.7978 -6.2443)',
                'finish_point' => 'POINT(106.7935 -6.2425)',
                'route' => 'LINESTRING(106.7978 -6.2443, 106.7995 -6.2432, 106.7935 -6.2425)',
                'distance' => 3.2,
                'estimated_duration' => 90,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_walking_2.jpg'],
            ],

            // 10. JAKARTA SELATAN: Tebet Eco Park Morning Stroll (Jalan Bareng Jaksel)
            [
                'activation_slug' => 'jalan-bareng-jaksel',
                'name' => 'Tebet Eco Park Morning Stroll & Eksplorasi Pohon Trembesi',
                'type' => 'walking',
                'description' => '<p>Jalan santai pagi di jembatan layang Infinity Link Bridge dan jalur setapak kayu Tebet Eco Park. Kita akan berdiskusi santai mengenai pentingnya ruang hijau terbuka bagi kesehatan mental warga kota.</p><p><strong>Titik Kumpul:</strong> Gerbang Utama Tebet Eco Park (06.30 WIB)</p>',
                'date' => Carbon::now()->addDays(15)->setHour(6)->setMinute(30),
                'poster' => 'events/media_walking_3.jpg',
                'registration_link' => 'https://instagram.com/jalanbarengind',
                'youtube_link' => null,
                'start_point' => 'POINT(106.8529 -6.2378)',
                'finish_point' => 'POINT(106.8529 -6.2378)',
                'route' => 'LINESTRING(106.8529 -6.2378, 106.8540 -6.2365, 106.8529 -6.2378)',
                'distance' => 2.8,
                'estimated_duration' => 75,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_walking_1.jpg'],
            ],

            // 11. BONE: Heritage Walk Arung Palakka (Jalan Bareng Bone)
            [
                'activation_slug' => 'jalan-bareng-bone',
                'name' => 'Heritage Walk: Napak Tilas Sejarah Bumi Arung Palakka di Watampone',
                'type' => 'walking',
                'description' => '<p>Jalan santai edukasi sejarah menyusuri jejak cagar budaya Kota Watampone. Dari Museum La Pawawoi, Rumah Adat Bola Soba, hingga sudut Lapangan Merdeka.</p><p><strong>Titik Kumpul:</strong> Halaman Museum La Pawawoi (06.00 WITA)</p>',
                'date' => Carbon::now()->addDays(11)->setHour(6)->setMinute(0),
                'poster' => 'events/media_heritage_2.jpg',
                'registration_link' => 'https://instagram.com/jalanbarengind',
                'youtube_link' => null,
                'start_point' => 'POINT(120.3280 -4.5385)',
                'finish_point' => 'POINT(120.3292 -4.5375)',
                'route' => 'LINESTRING(120.3280 -4.5385, 120.3265 -4.5420, 120.3292 -4.5375)',
                'distance' => 3.6,
                'estimated_duration' => 100,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_heritage_1.jpg'],
            ],

            // 12. GOWA: Jelajah Balla Lompoa (Jalan Bareng Gowa)
            [
                'activation_slug' => 'jalan-bareng-gowa',
                'name' => 'Morning Walk: Balla Lompoa ke Kompleks Makam Katangka',
                'type' => 'walking',
                'description' => '<p>Menelusuri sejarah kerajaan Gowa dengan berjalan kaki santai di bawah pohon peneduh kota Sungguminasa. Menghubungkan istana kayu Balla Lompoa dengan masjid tertua Katangka.</p><p><strong>Titik Kumpul:</strong> Gerbang Museum Balla Lompoa (06.30 WITA)</p>',
                'date' => Carbon::now()->addDays(17)->setHour(6)->setMinute(30),
                'poster' => 'events/media_heritage_1.jpg',
                'registration_link' => 'https://instagram.com/jalanbarengind',
                'youtube_link' => null,
                'start_point' => 'POINT(119.4532 -5.2038)',
                'finish_point' => 'POINT(119.4455 -5.1989)',
                'route' => 'LINESTRING(119.4532 -5.2038, 119.4500 -5.2010, 119.4455 -5.1989)',
                'distance' => 3.2,
                'estimated_duration' => 80,
                'user_id' => $communityAdmins->random()->id,
                'photos' => ['events/media_walking_2.jpg'],
            ]
        ];

        foreach ($events as $data) {
            $activation = $activations->get($data['activation_slug']);
            $activationId = $activation ? $activation->id : null;

            $event = Event::updateOrCreate(
                ['name' => $data['name']],
                [
                    'user_id' => $data['user_id'],
                    'activation_id' => $activationId,
                    'slug' => Str::slug($data['name']) . '-' . rand(1000, 9999),
                    'type' => $data['type'],
                    'description' => $data['description'],
                    'date' => $data['date'],
                    'poster' => $data['poster'],
                    'registration_link' => $data['registration_link'],
                    'youtube_link' => $data['youtube_link'],
                    'start_point' => $data['start_point'],
                    'finish_point' => $data['finish_point'],
                    'route' => $data['route'],
                    'distance' => $data['distance'],
                    'estimated_duration' => $data['estimated_duration'],
                    'likes_count' => rand(15, 65),
                    'comments_count' => rand(4, 22),
                    'participants_count' => rand(25, 80),
                ]
            );

            // Create or update event poster record
            EventPhoto::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'is_poster' => true,
                ],
                [
                    'photo_path' => $data['poster'],
                    'order' => 0,
                ]
            );
        }

        $this->command->info('EventSeeder executed: ' . count($events) . ' events seeded across all city chapters!');
    }
}
