<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Activation;

class UpdateActivationRelationsSeeder extends Seeder
{
    public function run(): void
    {
        // Get Jalan Bareng Makassar activation ID
        $activation = Activation::where('slug', 'jalan-bareng-makassar')->first();
        
        if (!$activation) {
            $this->command->error('Activation "Jalan Bareng Makassar" not found!');
            return;
        }
        
        $activationId = $activation->id;
        
        // Update all events to link to Jalan Bareng Makassar
        $eventsUpdated = DB::table('events')->update(['activation_id' => $activationId]);
        $this->command->info("Updated {$eventsUpdated} events to link to Jalan Bareng Makassar");
        
        // Update all destinations to link to Jalan Bareng Makassar
        $destinationsUpdated = DB::table('destinations')->update(['activation_id' => $activationId]);
        $this->command->info("Updated {$destinationsUpdated} destinations to link to Jalan Bareng Makassar");
        
        $this->command->info('All events and destinations have been linked to Jalan Bareng Makassar activation!');
    }
}
