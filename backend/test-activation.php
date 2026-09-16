<?php

// Quick test script to check activation API response
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$activation = App\Models\Activation::where('slug', 'jalan-bareng-makassar')->first();

if (!$activation) {
    echo "Activation not found!\n";
    exit(1);
}

echo "=== ACTIVATION DEBUG ===\n";
echo "ID: " . $activation->id . "\n";
echo "Name: " . $activation->name . "\n\n";

echo "--- Events ---\n";
$upcomingEvents = $activation->events()->where('date', '>=', now())->get();
echo "Upcoming events count: " . $upcomingEvents->count() . "\n";
foreach ($upcomingEvents->take(3) as $event) {
    echo "  - {$event->name} ({$event->date})\n";
}

echo "\n--- Destinations ---\n";
$destinations = $activation->destinations()->get();
echo "Destinations count: " . $destinations->count() . "\n";
foreach ($destinations->take(3) as $dest) {
    echo "  - {$dest->name}\n";
}

echo "\n--- Featured Event ---\n";
$featuredEvent = $activation->events()
    ->where('date', '>=', now())
    ->whereNotNull('poster')
    ->orderBy('date', 'asc')
    ->first();

if (!$featuredEvent) {
    $featuredEvent = $activation->events()
        ->where('date', '>=', now())
        ->orderBy('date', 'asc')
        ->first();
}

if ($featuredEvent) {
    echo "Featured event: {$featuredEvent->name}\n";
    echo "Has poster: " . ($featuredEvent->poster ? 'Yes' : 'No') . "\n";
} else {
    echo "No featured event found\n";
}

echo "\n======================\n";
