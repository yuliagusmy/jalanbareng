<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Kuliner', 'description' => 'Tempat makan dan kuliner khas Makassar'],
            ['name' => 'Wisata Alam', 'description' => 'Destinasi wisata alam dan taman'],
            ['name' => 'Sejarah', 'description' => 'Tempat bersejarah dan landmark'],
            ['name' => 'Spot Foto', 'description' => 'Lokasi menarik untuk berfoto'],
            ['name' => 'Pantai', 'description' => 'Pantai dan pesisir'],
            ['name' => 'Taman Kota', 'description' => 'Taman dan ruang publik'],
            ['name' => 'Belanja', 'description' => 'Pusat perbelanjaan dan pasar'],
            ['name' => 'Seni & Budaya', 'description' => 'Tempat seni dan budaya lokal'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
