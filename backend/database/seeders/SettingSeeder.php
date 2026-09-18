<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Footer Brand Settings
            [
                'key' => 'footer_brand_title',
                'value' => 'Jalan Bareng',
                'type' => 'text',
                'group' => 'footer',
            ],
            [
                'key' => 'footer_brand_description',
                'value' => 'Inisiatif ruang bersama untuk pejalan kaki, penjelajah sudut kota, dan komunitas kreatif di Indonesia. #menelusurikotalebihlambat',
                'type' => 'text',
                'group' => 'footer',
            ],
            
            // Social Media Links
            [
                'key' => 'footer_social_instagram',
                'value' => 'https://instagram.com/jalanbarengind',
                'type' => 'url',
                'group' => 'footer',
            ],
            [
                'key' => 'footer_social_facebook',
                'value' => 'https://facebook.com',
                'type' => 'url',
                'group' => 'footer',
            ],
            [
                'key' => 'footer_social_twitter',
                'value' => 'https://twitter.com',
                'type' => 'url',
                'group' => 'footer',
            ],

            // Community Movement Stats
            [
                'key' => 'community_tagline',
                'value' => '#menelusurikotalebihlambat',
                'type' => 'text',
                'group' => 'community',
            ],
            [
                'key' => 'stats_members',
                'value' => '1.000+',
                'type' => 'text',
                'group' => 'community',
            ],
            [
                'key' => 'stats_database_registrants',
                'value' => '10.000+',
                'type' => 'text',
                'group' => 'community',
            ],
            [
                'key' => 'stats_partners',
                'value' => '80+',
                'type' => 'text',
                'group' => 'community',
            ],
            [
                'key' => 'stats_cities',
                'value' => '5 Wilayah',
                'type' => 'text',
                'group' => 'community',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('   - Settings seeded successfully with official Jalan Bareng ecosystem data');
    }
}
