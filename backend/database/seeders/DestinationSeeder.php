<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\DestinationPhoto;
use App\Models\User;
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

        $destinations = [
            // Kuliner
            [
                'name' => 'Pantai Losari',
                'category_id' => 1, // Kuliner
                'description' => 'Ikon kota Makassar yang terkenal dengan sunset dan kuliner pinggir pantai. Tempat favorit warga untuk bersantai sambil menikmati Pisang Epe, Pallu Basa, dan Coto Makassar.',
                'lat' => -5.1477,
                'lng' => 119.4040,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Warung Coto Gagak',
                'category_id' => 1,
                'description' => 'Warung Coto legendaris yang sudah berdiri sejak puluhan tahun. Coto Makassar dengan kuah kental dan daging sapi empuk menjadi favorit warga lokal dan wisatawan.',
                'lat' => -5.1356,
                'lng' => 119.4225,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'RM Apong',
                'category_id' => 1,
                'description' => 'Rumah makan seafood terkenal dengan menu andalan kepiting saus padang dan ikan bakar. Tempat makan keluarga favorit dengan pemandangan laut.',
                'lat' => -5.1488,
                'lng' => 119.4050,
                'user_id' => $members->random()->id,
            ],

            // Wisata Alam
            [
                'name' => 'Tanjung Bayang Beach',
                'category_id' => 5, // Pantai
                'description' => 'Pantai dengan pasir putih dan air laut jernih. Cocok untuk berenang dan olahraga air. Terdapat fasilitas penyewaan banana boat dan jet ski.',
                'lat' => -5.1890,
                'lng' => 119.3940,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Pulau Kayangan (Lae-Lae)',
                'category_id' => 2, // Wisata Alam
                'description' => 'Pulau kecil di tengah laut yang dapat ditempuh dengan perahu. Tempat snorkeling terbaik di Makassar dengan terumbu karang yang masih indah.',
                'lat' => -5.1100,
                'lng' => 119.3833,
                'user_id' => $members->random()->id,
            ],

            // Sejarah
            [
                'name' => 'Fort Rotterdam',
                'category_id' => 3, // Sejarah
                'description' => 'Benteng peninggalan VOC yang kini menjadi museum La Galigo. Bangunan bersejarah dengan arsitektur khas kolonial Belanda yang masih terawat.',
                'lat' => -5.1348,
                'lng' => 119.4067,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Masjid Raya Makassar',
                'category_id' => 3,
                'description' => 'Masjid megah dengan arsitektur modern dan kubah emas yang ikonik. Landmark kota Makassar yang dapat dilihat dari berbagai sudut kota.',
                'lat' => -5.1371,
                'lng' => 119.4184,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Monumen Mandala',
                'category_id' => 3,
                'description' => 'Monumen bersejarah yang mengenang perjuangan rakyat dalam membebaskan Irian Barat. Terdapat museum dan taman yang luas.',
                'lat' => -5.1500,
                'lng' => 119.4315,
                'user_id' => $members->random()->id,
            ],

            // Spot Foto & Taman
            [
                'name' => 'Taman Macan',
                'category_id' => 6, // Taman Kota
                'description' => 'Taman kota yang ramai dikunjungi untuk olahraga pagi dan sore. Terdapat jogging track, area bermain anak, dan spot foto instagramable.',
                'lat' => -5.1445,
                'lng' => 119.4278,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Pantai Akkarena',
                'category_id' => 4, // Spot Foto
                'description' => 'Pantai dengan dermaga panjang yang menjadi spot foto favorit. Sunset di pantai ini sangat indah dengan background Pulau Lae-Lae.',
                'lat' => -5.1700,
                'lng' => 119.4020,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Jembatan Soekarno',
                'category_id' => 4,
                'description' => 'Jembatan ikonik yang menghubungkan Makassar dengan Maros. Spot foto favorit terutama saat sunset dengan pemandangan sungai Tallo.',
                'lat' => -5.0893,
                'lng' => 119.4526,
                'user_id' => $members->random()->id,
            ],

            // Belanja
            [
                'name' => 'Trans Studio Mall',
                'category_id' => 7, // Belanja
                'description' => 'Mal terbesar di Makassar dengan theme park Trans Studio World. Pusat perbelanjaan modern dengan berbagai tenant fashion dan F&B.',
                'lat' => -5.1542,
                'lng' => 119.4398,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Pasar Butung',
                'category_id' => 7,
                'description' => 'Pasar tradisional yang menjual berbagai oleh-oleh khas Makassar seperti kue kering, sarung sutera, dan kerajinan tangan.',
                'lat' => -5.1322,
                'lng' => 119.4193,
                'user_id' => $members->random()->id,
            ],

            // Seni & Budaya
            [
                'name' => 'Benteng Somba Opu',
                'category_id' => 8, // Seni & Budaya
                'description' => 'Benteng bersejarah dengan replika rumah adat dari seluruh Sulawesi Selatan. Tempat belajar budaya dan sejarah Sulawesi.',
                'lat' => -5.1978,
                'lng' => 119.3957,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Anjungan Pantai Losari',
                'category_id' => 8,
                'description' => 'Panggung terbuka untuk pertunjukan seni dan budaya. Sering diadakan event musik dan festival budaya Makassar.',
                'lat' => -5.1465,
                'lng' => 119.4055,
                'user_id' => $members->random()->id,
            ],

            // Tambahan Kuliner
            [
                'name' => 'Konro Karebosi',
                'category_id' => 1,
                'description' => 'Rumah makan spesialis Konro bakar dan sop konro. Daging iga sapi yang empuk dengan bumbu rempah khas Makassar.',
                'lat' => -5.1390,
                'lng' => 119.4180,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Sop Saudara Ibu Yayu',
                'category_id' => 1,
                'description' => 'Warung legendaris dengan menu sop saudara yang gurih dan hangat. Tempat sarapan favorit warga Makassar.',
                'lat' => -5.1420,
                'lng' => 119.4195,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Es Pisang Ijo Bacan',
                'category_id' => 1,
                'description' => 'Tempat es pisang ijo paling terkenal di Makassar. Pisang dibungkus adonan hijau dengan santan dan es serut yang menyegarkan.',
                'lat' => -5.1455,
                'lng' => 119.4240,
                'user_id' => $members->random()->id,
            ],

            // Tambahan Spot Foto
            [
                'name' => 'Taman Pelataran Bahari',
                'category_id' => 4,
                'description' => 'Taman dengan view laut lepas yang indah. Dilengkapi dengan gardu pandang dan spot foto dengan huruf MAKASSAR raksasa.',
                'lat' => -5.1500,
                'lng' => 119.4075,
                'user_id' => $members->random()->id,
            ],
            [
                'name' => 'Kawasan Karebosi Link',
                'category_id' => 4,
                'description' => 'Kawasan pedestrian modern dengan lampu hias dan mural seni. Spot foto hits anak muda Makassar.',
                'lat' => -5.1420,
                'lng' => 119.4165,
                'user_id' => $members->random()->id,
            ],
        ];

        foreach ($destinations as $data) {
            $photoPath = 'destinations/placeholder-' . rand(1000, 9999) . '.jpg';

            $destination = Destination::create([
                'user_id' => $data['user_id'],
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . rand(1000000, 9999999),
                'description' => $data['description'],
                'location' => "POINT({$data['lng']} {$data['lat']})",
                'primary_photo' => $photoPath,
                'likes_count' => rand(5, 50),
                'comments_count' => rand(2, 20),
            ]);

            // Create primary photo
            DestinationPhoto::create([
                'destination_id' => $destination->id,
                'photo_path' => $photoPath,
                'is_primary' => true,
                'order' => 0,
            ]);

            // Add 1-3 additional photos
            $photoCount = rand(1, 3);
            for ($i = 1; $i <= $photoCount; $i++) {
                DestinationPhoto::create([
                    'destination_id' => $destination->id,
                    'photo_path' => 'destinations/photo-' . $destination->id . '-' . $i . '.jpg',
                    'is_primary' => false,
                    'order' => $i,
                ]);
            }
        }

        $this->command->info('Created ' . count($destinations) . ' destinations with photos');
    }
}
