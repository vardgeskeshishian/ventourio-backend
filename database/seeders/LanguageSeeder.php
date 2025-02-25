<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $enJson = file_get_contents(base_path('lang/web/en.json'));
        $ruJson = file_get_contents(base_path('lang/web/ru.json'));


        Language::insert([
            [
                'code'  => 'en',
                'title_l'  => json_encode([
                    'en' => 'Eng',
                ]),
                'type' => 'ltl',
                'is_default' => true,
                'is_rtl' => false,
                'is_active' => true,
                'flag' => 'en.png',
                'localization_json' => $enJson
            ],
            [
                'code'  => 'ru',
                'title_l'  => json_encode([
                    'ru' => 'Rus',
                ]),
                'type' => 'ltl',
                'is_default' => false,
                'is_rtl' => false,
                'is_active' => true,
                'flag' => 'ru.png',
                'localization_json' => $ruJson
            ]
        ]);
    }
}
