<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::insert([
            [
                'name_l' => '{"ru": "Доллар", "en": "Usd"}',
                'code' => 'usd',
                'symbol' => '$',
                'is_main' => 1,
                'currency_rate' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
//            [
//                'name_l' => '{"ru": "Рубли", "en": "Ruble"}',
//                'code' => 'rub',
//                'symbol' => '₽',
//                'is_main' => 0,
//                'currency_rate' => 3.245,
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
        ]);
    }
}
