<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventPhoto;
use App\Models\User;
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

        $events = [
            // Upcoming Walking Events
            [
                'name' => 'Jelajah Kota Tua Makassar',
                'type' => 'walking',
                'description' => '<p>Mari jelajahi sejarah Kota Makassar melalui jalan kaki berkeliling kawasan kota tua. Kita akan mengunjungi Fort Rotterdam, Masjid Raya, dan bangunan bersejarah lainnya.</p><p><strong>Rute:</strong> Pantai Losari - Fort Rotterdam - Masjid Raya - Karebosi Link</p><p><strong>Yang perlu dibawa:</strong> Sepatu nyaman, topi, air minum, kamera</p>',
                'date' => Carbon::now()->addDays(7)->setHour(6)->setMinute(0),
                'registration_link' => 'https://forms.gle/abc123',
                'youtube_link' => null,
                'start_point' => 'POINT(119.4040 -5.1477)', // Pantai Losari
                'finish_point' => 'POINT(119.4165 -5.1420)', // Karebosi Link
                'route' => 'LINESTRING(119.4040 -5.1477, 119.4067 -5.1348, 119.4184 -5.1371, 119.4165 -5.1420)',
                'distance' => 5.2,
                'estimated_duration' => 120, // 2 hours
                'user_id' => $communityAdmins->random()->id,
            ],
            [
                'name' => 'Morning Walk: Pantai Losari ke Akkarena',
                'type' => 'walking',
                'description' => '<p>Jalan santai pagi menyusuri pesisir pantai Makassar. Nikmati sunrise dan udara segar sambil berolahraga bersama.</p><p><strong>Benefit:</strong> Olahraga sehat, networking, foto-foto cantik!</p><p><strong>Meeting Point:</strong> Pantai Losari (depan Anjungan)</p>',
                'date' => Carbon::now()->addDays(14)->setHour(5)->setMinute(30),
                'registration_link' => 'https://forms.gle/def456',
                'youtube_link' => null,
                'start_point' => 'POINT(119.4040 -5.1477)',
                'finish_point' => 'POINT(119.4020 -5.1700)',
                'route' => 'LINESTRING(119.4040 -5.1477, 119.4055 -5.1465, 119.4075 -5.1500, 119.4050 -5.1600, 119.4020 -5.1700)',
                'distance' => 4.5,
                'estimated_duration' => 90,
                'user_id' => $communityAdmins->random()->id,
            ],
            [
                'name' => 'Heritage Walk: Somba Opu Trail',
                'type' => 'walking',
                'description' => '<p>Petualangan sejarah di Benteng Somba Opu dan sekitarnya. Kita akan belajar tentang kerajaan Gowa dan melihat rumah adat Sulsel.</p><p><strong>Pemandu:</strong> Sejarawan lokal</p><p><strong>Biaya:</strong> Gratis (donasi sukarela)</p>',
                'date' => Carbon::now()->addDays(21)->setHour(7)->setMinute(0),
                'registration_link' => 'https://forms.gle/ghi789',
                'youtube_link' => null,
                'start_point' => 'POINT(119.3957 -5.1978)',
                'finish_point' => 'POINT(119.3957 -5.1978)', // Circular route
                'route' => 'LINESTRING(119.3957 -5.1978, 119.3970 -5.1990, 119.3940 -5.1890, 119.3950 -5.1960, 119.3957 -5.1978)',
                'distance' => 3.0,
                'estimated_duration' => 150, // 2.5 hours including tour
                'user_id' => $communityAdmins->random()->id,
            ],

            // Upcoming Regular Events
            [
                'name' => 'Workshop Fotografi Street Photography',
                'type' => 'regular',
                'description' => '<p>Belajar teknik street photography bersama fotografer profesional. Cocok untuk pemula maupun yang sudah berpengalaman.</p><p><strong>Materi:</strong></p><ul><li>Komposisi foto jalanan</li><li>Lighting dan angle</li><li>Editing dasar</li><li>Praktek langsung di kawasan Losari</li></ul><p><strong>Instruktur:</strong> Ahmad Rasyid (Fotografer National Geographic)</p>',
                'date' => Carbon::now()->addDays(10)->setHour(14)->setMinute(0),
                'registration_link' => 'https://forms.gle/jkl012',
                'youtube_link' => 'https://www.youtube.com/watch?v=example1',
                'start_point' => null,
                'finish_point' => null,
                'route' => null,
                'distance' => null,
                'estimated_duration' => 180,
                'user_id' => $communityAdmins->random()->id,
            ],
            [
                'name' => 'Kuliner Night: Wisata Kuliner Losari',
                'type' => 'regular',
                'description' => '<p>Jelajahi kuliner malam di Pantai Losari dan sekitarnya. Kita akan mencicipi berbagai makanan khas Makassar!</p><p><strong>Menu yang akan dicoba:</strong></p><ul><li>Coto Makassar</li><li>Pisang Epe</li><li>Konro Bakar</li><li>Es Pisang Ijo</li><li>Dan lain-lain!</li></ul><p><strong>Biaya:</strong> Rp 100.000 (sudah termasuk semua makanan)</p>',
                'date' => Carbon::now()->addDays(12)->setHour(18)->setMinute(0),
                'registration_link' => 'https://forms.gle/mno345',
                'youtube_link' => null,
                'start_point' => null,
                'finish_point' => null,
                'route' => null,
                'distance' => null,
                'estimated_duration' => 180,
                'user_id' => $communityAdmins->random()->id,
            ],
            [
                'name' => 'Gathering: Meet & Greet Member Baru',
                'type' => 'regular',
                'description' => '<p>Acara perkenalan untuk member baru komunitas Jalan Bareng. Ayo kenalan dan bangun networking!</p><p><strong>Agenda:</strong></p><ul><li>Perkenalan member baru</li><li>Games & Ice breaking</li><li>Sharing pengalaman jalan-jalan</li><li>Makan bersama</li></ul><p><strong>Tempat:</strong> Cafe Losari Beach<br><strong>Dress code:</strong> Casual, wear our merchandise!</p>',
                'date' => Carbon::now()->addDays(5)->setHour(16)->setMinute(0),
                'registration_link' => 'https://forms.gle/pqr678',
                'youtube_link' => null,
                'start_point' => null,
                'finish_point' => null,
                'route' => null,
                'distance' => null,
                'estimated_duration' => 120,
                'user_id' => $communityAdmins->random()->id,
            ],

            // Past Events
            [
                'name' => 'Makassar Car Free Day Walk',
                'type' => 'walking',
                'description' => '<p>Jalan santai saat Car Free Day menyusuri Jalan Penghibur. Event bulanan rutin komunitas.</p>',
                'date' => Carbon::now()->subDays(7)->setHour(6)->setMinute(0),
                'registration_link' => null,
                'youtube_link' => 'https://www.youtube.com/watch?v=example2',
                'start_point' => 'POINT(119.4220 -5.1370)',
                'finish_point' => 'POINT(119.4220 -5.1370)',
                'route' => 'LINESTRING(119.4220 -5.1370, 119.4240 -5.1380, 119.4260 -5.1370, 119.4240 -5.1360, 119.4220 -5.1370)',
                'distance' => 2.5,
                'estimated_duration' => 60,
                'user_id' => $communityAdmins->random()->id,
            ],
            [
                'name' => 'Cleanup Day: Bersih-bersih Pantai',
                'type' => 'regular',
                'description' => '<p>Aksi bersih-bersih pantai bersama komunitas peduli lingkungan. Sukses mengumpulkan 50 kg sampah!</p><p>Terima kasih untuk semua volunteer yang sudah berpartisipasi.</p>',
                'date' => Carbon::now()->subDays(14)->setHour(7)->setMinute(0),
                'registration_link' => null,
                'youtube_link' => 'https://www.youtube.com/watch?v=example3',
                'start_point' => null,
                'finish_point' => null,
                'route' => null,
                'distance' => null,
                'estimated_duration' => 180,
                'user_id' => $communityAdmins->random()->id,
            ],
            [
                'name' => 'Festival Budaya Makassar 2025',
                'type' => 'regular',
                'description' => '<p>Perayaan budaya Makassar dengan berbagai pertunjukan tari, musik tradisional, dan pameran kuliner.</p><p>Event spektakuler yang dihadiri 500+ peserta!</p>',
                'date' => Carbon::now()->subDays(30)->setHour(9)->setMinute(0),
                'registration_link' => null,
                'youtube_link' => 'https://www.youtube.com/watch?v=example4',
                'start_point' => null,
                'finish_point' => null,
                'route' => null,
                'distance' => null,
                'estimated_duration' => 480, // 8 hours
                'user_id' => $communityAdmins->random()->id,
            ],
        ];

        foreach ($events as $data) {
            $posterPath = 'events/poster-' . rand(1000, 9999) . '.jpg';

            $event = Event::create([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . rand(1000000, 9999999),
                'type' => $data['type'],
                'description' => $data['description'],
                'date' => $data['date'],
                'poster' => $posterPath,
                'registration_link' => $data['registration_link'],
                'youtube_link' => $data['youtube_link'],
                'start_point' => $data['start_point'],
                'finish_point' => $data['finish_point'],
                'route' => $data['route'],
                'distance' => $data['distance'],
                'estimated_duration' => $data['estimated_duration'],
                'likes_count' => rand(10, 100),
                'comments_count' => rand(5, 30),
                'participants_count' => rand(20, 150),
            ]);

            // Create event poster
            EventPhoto::create([
                'event_id' => $event->id,
                'photo_path' => $posterPath,
                'is_poster' => true,
                'order' => 0,
            ]);

            // Add 1-2 additional photos for past events
            if ($event->date->isPast()) {
                $photoCount = rand(1, 2);
                for ($i = 1; $i <= $photoCount; $i++) {
                    EventPhoto::create([
                        'event_id' => $event->id,
                        'photo_path' => 'events/photo-' . $event->id . '-' . $i . '.jpg',
                        'is_poster' => false,
                        'order' => $i,
                    ]);
                }
            }
        }

        $this->command->info('Created ' . count($events) . ' events with photos');
    }
}
