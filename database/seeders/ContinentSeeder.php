<?php

namespace Database\Seeders;

use App\Models\Continent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContinentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $continents = [
            'Europe',
            'Asia',
            'North America',
            'Ocean',
            'South America',
            'Africa'
        ];

        foreach ($continents as $continent) {
            Continent::factory(1)->create(['title_l' => ['en' => $continent]]);
        }
    }
}
