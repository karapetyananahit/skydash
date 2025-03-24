<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialMediasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('social_medias')->insert([
            ['name' => 'Youtube Integration', 'price' => 3500],
            ['name' => 'Youtube Dedicated', 'price' => 8000],
            ['name' => 'Youtube Short', 'price' => 3000],
            ['name' => 'Instagram Reel', 'price' => 2500],
            ['name' => 'Instagram Story', 'price' => 1000],
            ['name' => 'TikTok', 'price' => 2500],
            ['name' => 'Facebook Post', 'price' => 3000],
        ]);

    }
}
