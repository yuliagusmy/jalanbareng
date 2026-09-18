<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Destination;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
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

        // Seed Likes for Destinations
        foreach ($destinations as $destination) {
            $likersCount = rand(5, min(15, $users->count()));
            $likers = $users->random($likersCount);

            foreach ($likers as $user) {
                Like::firstOrCreate([
                    'user_id' => $user->id,
                    'likeable_id' => $destination->id,
                    'likeable_type' => Destination::class,
                ]);
            }

            $destination->update([
                'likes_count' => Like::where('likeable_id', $destination->id)
                    ->where('likeable_type', Destination::class)
                    ->count(),
            ]);
        }

        // Seed Likes for Events
        foreach ($events as $event) {
            $likersCount = rand(6, min(18, $users->count()));
            $likers = $users->random($likersCount);

            foreach ($likers as $user) {
                Like::firstOrCreate([
                    'user_id' => $user->id,
                    'likeable_id' => $event->id,
                    'likeable_type' => Event::class,
                ]);
            }

            $event->update([
                'likes_count' => Like::where('likeable_id', $event->id)
                    ->where('likeable_type', Event::class)
                    ->count(),
            ]);
        }

        $this->command->info('LikeSeeder executed: seeded real likes and updated counters for destinations and events!');
    }
}
