<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\DestinationPhoto;
use App\Models\User;
use App\Models\Activation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $members = User::whereHas('role', fn($q) => $q->where('name', 'member'))->get();

        if ($members->isEmpty()) {
            $this->command->warn('No members found. Please run UserSeeder first.');
            return;
        }

        // Activations lookup
        $activations = Activation::all()->keyBy('slug');
        $makassarId = $activations['jalan-bareng-makassar']->id ?? null;
        $makanId = $activations['makan-bareng']->id ?? $makassarId;
        $jakselId = $activations['jalan-bareng-jaksel']->id ?? $makassarId;
        $palopoId = $activations['jalan-bareng-palopo']->id ?? $makassarId;
        $gowaId = $activations['jalan-bareng-gowa']->id ?? $makassarId;
        $boneId = $activations['jalan-bareng-bone']->id ?? $makassarId;
        $spaceId = $activations['jalan-bareng-creative-space']->id ?? $makassarId;

        $destinations = [
            // ==================== MAKASSAR (Chapter Makassar & Makan Bareng) ====================
            [
                'name' => 'Pantai Losari',
                'category_id' => 1, // Kuliner
                'activation_id' => $makanId,
                'description' => 'Ikon kota Makassar yang terkenal dengan sunset dan kuliner pinggir pantai. Tempat favorit warga untuk bersantai sambil menikmati Pisang Epe, Pallu Basa, dan Coto Makassar.',
                'lat' => -5.1477,
                'lng' => 119.4040,
                'photo_path' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Warung Coto Gagak',
                'category_id' => 1,
                'activation_id' => $makanId,
                'description' => 'Warung Coto legendaris yang sudah berdiri sejak puluhan tahun. Coto Makassar dengan kuah kental rempah dan daging sapi empuk menjadi favorit warga lokal dan wisatawan.',
                'lat' => -5.1356,
                'lng' => 119.4225,
                'photo_path' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'RM Apong Seafood',
                'category_id' => 1,
                'activation_id' => $makanId,
                'description' => 'Rumah makan seafood terkenal dengan menu andalan kepiting saus padang dan ikan bakar parape. Tempat makan keluarga favorit dengan pemandangan laut.',
                'lat' => -5.1488,
                'lng' => 119.4050,
                'photo_path' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Fort Rotterdam',
                'category_id' => 3, // Sejarah
                'activation_id' => $makassarId,
                'description' => 'Benteng peninggalan VOC yang kini menjadi museum La Galigo. Bangunan cagar budaya dengan arsitektur khas kolonial yang masih terawat dan asri untuk berjalan santai.',
                'lat' => -5.1348,
                'lng' => 119.4067,
                'photo_path' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Taman Macan',
                'category_id' => 6, // Taman Kota
                'activation_id' => $makassarId,
                'description' => 'Taman kota yang rindang dan ramai dikunjungi untuk jalan pagi dan olahraga sore. Terdapat jogging track yang ramah pejalan, area bermain anak, dan bangku teduh.',
                'lat' => -5.1445,
                'lng' => 119.4278,
                'photo_path' => 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Kawasan Karebosi Link',
                'category_id' => 4, // Spot Foto
                'activation_id' => $makassarId,
                'description' => 'Kawasan pedestrian modern di jantung kota Makassar dengan pepohonan rindang dan instalasi seni. Tempat favorit anak muda untuk berkumpul seusai jalan sore.',
                'lat' => -5.1420,
                'lng' => 119.4165,
                'photo_path' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Jalan Somba Opu Pecinan',
                'category_id' => 7, // Belanja & Budaya
                'activation_id' => $makassarId,
                'description' => 'Pusat cenderamata, toko emas tradisional, dan warung kopi peranakan legendaris. Jalur pedestrian yang asyik disusuri untuk fotografi jalanan.',
                'lat' => -5.1388,
                'lng' => 119.4072,
                'photo_path' => 'https://images.unsplash.com/photo-1477959858617-67f30bc75b82?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Jalan Bareng Creative Space Makassar',
                'category_id' => 8, // Seni & Budaya
                'activation_id' => $spaceId,
                'description' => 'Simpul ruang komunal pejalan kaki dan perajin kriya. Tempat penyelenggaraan pameran mini, diskusi buku mingguan, dan titik kumpul sebelum jalan bareng.',
                'lat' => -5.1460,
                'lng' => 119.4190,
                'photo_path' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=800&h=600&fit=crop',
            ],

            // ==================== JAKARTA SELATAN (Chapter Jaksel) ====================
            [
                'name' => 'Taman Literasi Martha Christina Tiahahu Blok M',
                'category_id' => 6, // Taman Kota
                'activation_id' => $jakselId,
                'description' => 'Taman kota terpadu di kawasan transit Blok M. Dilengkapi dengan perpustakaan publik, amfiteater terbuka, kolam refleksi, dan akses langsung ke Stasiun MRT.',
                'lat' => -6.2443,
                'lng' => 106.7978,
                'photo_path' => 'https://images.unsplash.com/photo-1555899434-94d1368aa7af?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Tebet Eco Park',
                'category_id' => 6, // Taman Kota
                'activation_id' => $jakselId,
                'description' => 'Taman ekologis seluas 7 hektar dengan Infinity Link Bridge ikonik, zona rawa alami, dan jalur pejalan kaki kayu yang sangat nyaman di bawah kanopi pohon trembesi.',
                'lat' => -6.2378,
                'lng' => 106.8529,
                'photo_path' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Jalur Pedestrian Barito & Taman Ayodya',
                'category_id' => 4, // Spot Foto & Santai
                'activation_id' => $jakselId,
                'description' => 'Koridor pejalan kaki asri di Kebayoran Baru yang menghubungkan Taman Ayodya dan penjual tanaman hias. Sangat teduh untuk jalan pagi bersama teman.',
                'lat' => -6.2425,
                'lng' => 106.7935,
                'photo_path' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'M Bloc Space',
                'category_id' => 8, // Seni & Budaya
                'activation_id' => $jakselId,
                'description' => 'Ruang kreatif hasil pemanfaatan rumah dinas Percetakan Uang RI. Menampung toko rekaman independen, kedai kopi lokal, dan ruang pamer seni ramah pejalan.',
                'lat' => -6.2432,
                'lng' => 106.7995,
                'photo_path' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Hutan Kota GBK Senayan',
                'category_id' => 6, // Taman Kota
                'activation_id' => $jakselId,
                'description' => 'Hamparan rumput hijau luas dengan latar panorama gedung bertingkat Sudirman. Tempat favorit komunitas pejalan untuk piknik sore seusai menyusuri trotoar Sudirman.',
                'lat' => -6.2208,
                'lng' => 106.8042,
                'photo_path' => 'https://images.unsplash.com/photo-1477959858617-67f30bc75b82?w=800&h=600&fit=crop',
            ],

            // ==================== PALOPO (Chapter Palopo) ====================
            [
                'name' => 'Kawasan Pelabuhan Tanjung Ringgit Palopo',
                'category_id' => 5, // Pantai & Pelabuhan
                'activation_id' => $palopoId,
                'description' => 'Dermaga pesisir Teluk Bone dengan pemandangan perbukitan hijau. Titik kumpul utama komunitas Jalan Bareng Palopo untuk jalan pagi sambil menikmati udara laut segar.',
                'lat' => -2.9961,
                'lng' => 120.2038,
                'photo_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Istana Datu Luwu & Museum Batara Guru',
                'category_id' => 3, // Sejarah
                'activation_id' => $palopoId,
                'description' => 'Pusat cagar budaya Kedatuan Luwu yang sarat nilai sejarah maritim dan adat Tana Luwu. Halamannya yang luas sangat nyaman untuk jalan santai edukasi sejarah.',
                'lat' => -2.9934,
                'lng' => 120.1982,
                'photo_path' => 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Pantai Labombo Palopo',
                'category_id' => 5, // Pantai
                'activation_id' => $palopoId,
                'description' => 'Pantai berpasir teduh dengan rimbun pohon kelapa dan gazebo bambu. Rute jalan kaki favorit di akhir pekan untuk melepas penat dan bersantap kuliner kapurung.',
                'lat' => -2.9752,
                'lng' => 120.2115,
                'photo_path' => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Lapangan Gaspa Palopo',
                'category_id' => 6, // Taman & Ruang Publik
                'activation_id' => $palopoId,
                'description' => 'Alun-alun terbuka hijau di pusat kota Palopo. Titik temu warga untuk berolahraga, senam pagi, dan jalan kaki keliling lapangan yang bebas polusi kendaraan.',
                'lat' => -2.9945,
                'lng' => 120.1942,
                'photo_path' => 'https://images.unsplash.com/photo-1572949645841-094f3a9c4c94?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Masjid Jami Tua Palopo',
                'category_id' => 3, // Sejarah
                'activation_id' => $palopoId,
                'description' => 'Masjid tertua di Sulawesi Selatan yang dibangun pada tahun 1604 dengan dinding batu gunung dan kapur. Dikelilingi pepohonan tua yang menenangkan pejalan kaki.',
                'lat' => -2.9950,
                'lng' => 120.1970,
                'photo_path' => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?w=800&h=600&fit=crop',
            ],

            // ==================== GOWA (Chapter Gowa) ====================
            [
                'name' => 'Benteng Somba Opu Gowa',
                'category_id' => 3, // Sejarah
                'activation_id' => $gowaId,
                'description' => 'Benteng pertahanan utama Kerajaan Gowa abad ke-16. Di dalam kawasan ini terdapat miniatur rumah adat suku-suku di Sulawesi Selatan dengan jalur setapak yang rindang.',
                'lat' => -5.1978,
                'lng' => 119.3957,
                'photo_path' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Museum Balla Lompoa Sungguminasa',
                'category_id' => 3, // Sejarah
                'activation_id' => $gowaId,
                'description' => 'Rekonstruksi istana kayu megah Kerajaan Gowa yang didirikan dari kayu ulin kokoh. Halaman rumput terbukanya sering digunakan untuk sesi peregangan seusai jalan kaki.',
                'lat' => -5.2038,
                'lng' => 119.4532,
                'photo_path' => 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Kompleks Cagar Budaya Makam Katangka',
                'category_id' => 3, // Sejarah
                'activation_id' => $gowaId,
                'description' => 'Situs bersejarah makam raja-raja Gowa berdampingan dengan Masjid Tua Katangka. Jalur jalan kaki di sekitarnya teduh dengan arsitektur makam berkubah khas abad pertengahan.',
                'lat' => -5.1989,
                'lng' => 119.4455,
                'photo_path' => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Hutan Pinus Malino Gowa',
                'category_id' => 2, // Wisata Alam
                'activation_id' => $gowaId,
                'description' => 'Kawasan perbukitan sejuk di kaki Gunung Bawakaraeng dengan deretan pohon pinus menjulang. Rute walking tour favorit komunitas dengan udara pegunungan yang segar.',
                'lat' => -5.2530,
                'lng' => 119.8520,
                'photo_path' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=800&h=600&fit=crop',
            ],

            // ==================== BONE (Chapter Bone) ====================
            [
                'name' => 'Museum La Pawawoi Watampone',
                'category_id' => 3, // Sejarah
                'activation_id' => $boneId,
                'description' => 'Bekas istana Raja Bone ke-32 yang kini difungsikan sebagai museum cagar budaya. Menyimpan pusaka kerajaan dan berlokasi strategis di pusat pedestrian Watampone.',
                'lat' => -4.5385,
                'lng' => 120.3280,
                'photo_path' => 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Rumah Adat Bola Soba Bone',
                'category_id' => 3, // Sejarah
                'activation_id' => $boneId,
                'description' => 'Rumah panggung tradisional suku Bugis yang sarat filosofi arsitektur lokal. Halaman luasnya kerap menjadi titik awal rute jalan santai pagi komunitas.',
                'lat' => -4.5420,
                'lng' => 120.3265,
                'photo_path' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Lapangan Merdeka Watampone',
                'category_id' => 6, // Taman Kota
                'activation_id' => $boneId,
                'description' => 'Alun-alun kota Bone yang lapang dan rindang. Jalur pedestrian mengelilingi lapangan sangat digemari warga untuk olahraga jalan santai di pagi dan petang hari.',
                'lat' => -4.5375,
                'lng' => 120.3292,
                'photo_path' => 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Pelabuhan Bajoe Bone',
                'category_id' => 5, // Pantai & Pelabuhan
                'activation_id' => $boneId,
                'description' => 'Pelabuhan penyeberangan bersejarah di pesisir Teluk Bone. Jalanan panjang menuju dermaga menawarkan panorama laut lepas yang memukau untuk jalan sore.',
                'lat' => -4.5290,
                'lng' => 120.3950,
                'photo_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
            ],
        ];

        foreach ($destinations as $data) {
            $user = $members->random();
            $photoPath = $data['photo_path'];

            $destination = Destination::updateOrCreate(
                ['name' => $data['name']],
                [
                    'user_id' => $user->id,
                    'category_id' => $data['category_id'],
                    'activation_id' => $data['activation_id'],
                    'slug' => Str::slug($data['name']) . '-' . rand(1000, 9999),
                    'description' => $data['description'],
                    'location' => "POINT({$data['lng']} {$data['lat']})",
                    'primary_photo' => $photoPath,
                    'likes_count' => rand(12, 48),
                    'comments_count' => rand(3, 15),
                ]
            );

            // Update primary photo record
            DestinationPhoto::updateOrCreate(
                [
                    'destination_id' => $destination->id,
                    'is_primary' => true,
                ],
                [
                    'photo_path' => $photoPath,
                    'order' => 0,
                ]
            );
        }

        $this->command->info('DestinationSeeder executed: ' . count($destinations) . ' destinations seeded across all city chapters!');
    }
}
