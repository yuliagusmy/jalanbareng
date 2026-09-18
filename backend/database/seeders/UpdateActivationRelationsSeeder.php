<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Activation;

class UpdateActivationRelationsSeeder extends Seeder
{
    public function run(): void
    {
        $makassar = Activation::where('slug', 'jalan-bareng-makassar')->first();
        $makan = Activation::where('slug', 'makan-bareng')->first();
        $gowa = Activation::where('slug', 'jalan-bareng-gowa')->first();
        
        if (!$makassar) {
            $this->command->error('Activation "Jalan Bareng Makassar" not found!');
            return;
        }
        
        // Update unassigned events to default Makassar
        $eventsUpdated = DB::table('events')
            ->whereNull('activation_id')
            ->update(['activation_id' => $makassar->id]);
        $this->command->info("Updated {$eventsUpdated} unassigned events to link to Jalan Bareng Makassar");
        
        // Link culinary destinations to Makan Bareng activation
        if ($makan) {
            DB::table('destinations')
                ->where('category_id', 1) // Kuliner
                ->update(['activation_id' => $makan->id]);
        }
        
        // Link Gowa historical destinations to Gowa activation
        if ($gowa) {
            DB::table('destinations')
                ->where('name', 'like', '%Somba Opu%')
                ->update(['activation_id' => $gowa->id]);
        }

        // Link all remaining unassigned destinations to Makassar
        $destinationsUpdated = DB::table('destinations')
            ->whereNull('activation_id')
            ->update(['activation_id' => $makassar->id]);
        $this->command->info("Updated {$destinationsUpdated} destinations to link to activations");
    }
}
