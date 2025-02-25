<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultFacilities = config('goglobal.booking.facilities');

        $facilities = [];
        foreach ($defaultFacilities as $facility) {

            $facilities[] = [
                'title_l' => json_encode(['en' => $facility])
            ];
        }

        Facility::insert($facilities);
    }
}
