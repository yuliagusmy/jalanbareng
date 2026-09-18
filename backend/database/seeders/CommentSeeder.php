<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Destination;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $destinations = Destination::all();
        $events = Event::all();

        $destinationComments = [
            [
                'content' => 'Jalur pedestrian di sekitar sini sangat asri dan ramah pejalan kaki. Banyak pohon peneduh yang bikin jalan siang hari tetap nyaman.',
                'reply' => 'Betul kak, apalagi kalau lewat sini pas pagi sebelum jam delapan, udaranya masih segar banget.',
            ],
            [
                'content' => 'Apakah rute ini aman dan nyaman untuk lansia atau pengguna kursi roda?',
                'reply' => 'Aman kak, trotoarnya lebar, permukaannya rata, dan ada ramp landai di setiap persimpangan.',
            ],
            [
                'content' => 'Spot foto di dekat taman dan bangunan cagar budayanya estetik banget pas golden hour sore hari!',
                'reply' => 'Setuju! Jangan lupa bawa kamera dan botol minum sendiri biar tetap terhidrasi.',
            ],
            [
                'content' => 'Senang sekali menemukan tempat jalan santai yang tenang di tengah kota. Tempat favorit buat jalan sore sepulang kerja.',
                'reply' => 'Sama kak, sering mampir ke sini juga buat meluruskan kaki sambil ngobrol santai.',
            ],
        ];

        $eventComments = [
            [
                'content' => 'Wah seru banget agendanya! Titik kumpulnya di sebelah mana ya kak biar gampang ketemu rombongan?',
                'reply' => 'Titik kumpul di plaza utama dekat gerbang masuk kak. Nanti ada teman-teman panitia yang pakai kaos Jalan Bareng.',
            ],
            [
                'content' => 'Rute jalan santainya kira-kira berapa kilometer ya? Pemula yang jarang jalan kaki kuat nggak?',
                'reply' => 'Rata-rata 3 sampai 4 km dengan tempo santai kak, banyak jeda istirahatnya jadi sangat ramah pemula.',
            ],
            [
                'content' => 'Bolehkah ajak keluarga atau anak-anak ikut kegiatan ini?',
                'reply' => 'Sangat boleh kak! Inisiatif ini terbuka untuk semua kalangan dan segala usia.',
            ],
        ];

        // Seed Destination Comments
        foreach ($destinations as $destination) {
            $sample = $destinationComments[array_rand($destinationComments)];
            $author = $users->random();

            $rootComment = Comment::create([
                'user_id' => $author->id,
                'commentable_id' => $destination->id,
                'commentable_type' => Destination::class,
                'parent_id' => null,
                'content' => $sample['content'],
                'likes_count' => rand(2, 12),
            ]);

            // Add reply
            $replier = $users->where('id', '!=', $author->id)->random();
            Comment::create([
                'user_id' => $replier->id,
                'commentable_id' => $destination->id,
                'commentable_type' => Destination::class,
                'parent_id' => $rootComment->id,
                'content' => $sample['reply'],
                'likes_count' => rand(1, 8),
            ]);

            // Update destination comments_count
            $destination->update([
                'comments_count' => Comment::where('commentable_id', $destination->id)
                    ->where('commentable_type', Destination::class)
                    ->count(),
            ]);
        }

        // Seed Event Comments
        foreach ($events as $event) {
            $sample = $eventComments[array_rand($eventComments)];
            $author = $users->random();

            $rootComment = Comment::create([
                'user_id' => $author->id,
                'commentable_id' => $event->id,
                'commentable_type' => Event::class,
                'parent_id' => null,
                'content' => $sample['content'],
                'likes_count' => rand(3, 15),
            ]);

            // Add reply
            $replier = $users->where('id', '!=', $author->id)->random();
            Comment::create([
                'user_id' => $replier->id,
                'commentable_id' => $event->id,
                'commentable_type' => Event::class,
                'parent_id' => $rootComment->id,
                'content' => $sample['reply'],
                'likes_count' => rand(1, 9),
            ]);

            // Update event comments_count
            $event->update([
                'comments_count' => Comment::where('commentable_id', $event->id)
                    ->where('commentable_type', Event::class)
                    ->count(),
            ]);
        }

        $this->command->info('CommentSeeder executed: seeded realistic comments and replies for destinations and events!');
    }
}
