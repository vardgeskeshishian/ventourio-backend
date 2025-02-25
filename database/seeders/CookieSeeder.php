<?php

namespace Database\Seeders;

use App\Models\Cookie;
use Illuminate\Database\Seeder;

class CookieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cookie::insert([
                [
                    'title_l' => '{"ru": "Строго НЕОБХОДИМЫЕ куки", "en": "Strictly NEcessary Cookies"}',
                    'description_l' => '{"ru": "Эти трекеры помогают нам измерять трафик и анализировать ваше поведение с целью улучшения нашего сервиса.", "en": "These trackers help us to measure traffic and analyze your behavior with the goal of improving our service."}',
                    'key' => 'necessary',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title_l' => '{"ru": "Аналитические куки", "en": "Analytical Cookies"}',
                    'description_l' => '{"ru": "Эти трекеры помогают нам измерять трафик и анализировать ваше поведение с целью улучшения нашего сервиса.", "en": "These trackers help us to measure traffic and analyze your behavior with the goal of improving our service."}',
                    'key' => 'analytics',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title_l' => '{"ru": "Таргетинговые куки", "en": "Targeting cookies"}',
                    'description_l' => '{"ru": "Эти трекеры помогают нам измерять трафик и анализировать ваше поведение с целью улучшения нашего сервиса.", "en": "These trackers help us to measure traffic and analyze your behavior with the goal of improving our service."}',
                    'key' => 'targeting',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }
}
