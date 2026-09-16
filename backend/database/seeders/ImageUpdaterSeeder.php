<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\DestinationPhoto;
use App\Models\Event;
use App\Models\EventPhoto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageUpdaterSeeder extends Seeder
{
    public function run(): void
    {
        // Mapping destinations to images
        $destinationImages = [
            'Pantai Losari' => 'destinations/pantai-1.jpg',
            'Warung Coto Gagak' => 'destinations/coto.jpg',
            'RM Apong' => 'destinations/seafood.jpg',
            'Tanjung Bayang Beach' => 'destinations/pantai-2.jpg',
            'Pulau Kayangan (Lae-Lae)' => 'destinations/island.jpg',
            'Fort Rotterdam' => 'destinations/fort.jpg',
            'Masjid Raya Makassar' => 'destinations/mosque.jpg',
            'Monumen Mandala' => 'destinations/monument.jpg',
            'Taman Macan' => 'destinations/park.jpg',
            'Pantai Akkarena' => 'destinations/pantai-2.jpg',
            'Jembatan Soekarno' => 'destinations/bridge.jpg',
            'Trans Studio Mall' => 'destinations/mall.jpg',
            'Pasar Butung' => 'destinations/market.jpg',
            'Benteng Somba Opu' => 'destinations/fort.jpg',
            'Anjungan Pantai Losari' => 'destinations/pantai-1.jpg',
            'Konro Karebosi' => 'destinations/food-1.jpg',
            'Sop Saudara Ibu Yayu' => 'destinations/food-2.jpg',
            'Es Pisang Ijo Bacan' => 'destinations/dessert.jpg',
            'Taman Pelataran Bahari' => 'destinations/park.jpg',
            'Kawasan Karebosi Link' => 'destinations/bridge.jpg',
        ];

        $this->command->info('Updating destination images...');
        foreach ($destinationImages as $name => $imagePath) {
            $destination = Destination::where('name', $name)->first();
            if ($destination) {
                // Update primary photo
                $destination->primary_photo = $imagePath;
                $destination->save();

                // Update photo record
                DestinationPhoto::where('destination_id', $destination->id)
                    ->where('is_primary', true)
                    ->update(['photo_path' => $imagePath]);

                $this->command->info("Updated: {$name}");
            }
        }

        // Mapping events to images
        $eventImages = [
            'Jelajah Kota Tua Makassar' => 'events/walking-1.jpg',
            'Morning Walk: Pantai Losari ke Akkarena' => 'events/walking-2.jpg',
            'Heritage Walk: Somba Opu Trail' => 'events/walking-3.jpg',
            'Workshop Fotografi Street Photography' => 'events/photography.jpg',
            'Kuliner Night: Wisata Kuliner Losari' => 'events/culinary.jpg',
            'Gathering: Meet & Greet Member Baru' => 'events/gathering.jpg',
            'Makassar Car Free Day Walk' => 'events/walking-1.jpg',
            'Cleanup Day: Bersih-bersih Pantai' => 'events/cleanup.jpg',
            'Festival Budaya Makassar 2025' => 'events/festival.jpg',
        ];

        $this->command->info('Updating event images...');
        foreach ($eventImages as $name => $imagePath) {
            $event = Event::where('name', $name)->first();
            if ($event) {
                // Update poster
                $event->poster = $imagePath;
                $event->save();

                // Update photo record
                EventPhoto::where('event_id', $event->id)
                    ->where('is_poster', true)
                    ->update(['photo_path' => $imagePath]);

                $this->command->info("Updated: {$name}");
            }
        }

        $this->command->info('All images updated successfully!');
    }
}
