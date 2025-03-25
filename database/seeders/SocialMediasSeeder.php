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
            ['name' => 'Youtube Integration'],
            ['name' => 'Youtube Dedicated'],
            ['name' => 'Youtube Short'],
            ['name' => 'Instagram Reel'],
            ['name' => 'Instagram Story'],
            ['name' => 'TikTok'],
            ['name' => 'Facebook Post'],
        ]);

    }
}
