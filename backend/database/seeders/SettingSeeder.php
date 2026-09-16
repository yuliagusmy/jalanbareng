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
                'value' => 'Platform komunitas untuk berbagi dan menemukan destinasi menarik, mengikuti event jalan kaki, dan terhubung dengan komunitas lokal di Makassar.',
                'type' => 'text',
                'group' => 'footer',
            ],
            
            // Social Media Links
            [
                'key' => 'footer_social_facebook',
                'value' => 'https://facebook.com',
                'type' => 'url',
                'group' => 'footer',
            ],
            [
                'key' => 'footer_social_instagram',
                'value' => 'https://instagram.com',
                'type' => 'url',
                'group' => 'footer',
            ],
            [
                'key' => 'footer_social_twitter',
                'value' => 'https://twitter.com',
                'type' => 'url',
                'group' => 'footer',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('   - Footer settings created successfully');
    }
}
