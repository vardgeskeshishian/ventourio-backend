<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilityCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = $this->getCategories();

        FacilityCategory::insert($categories);
    }

    private function getCategories(): array
    {
        return [
            [
                'title_l' => json_encode([
                    'en' => 'General'
                ])
            ],
            [
                'title_l' => json_encode([
                    'en' => 'Services'
                ])
            ],
            [
                'title_l' => json_encode([
                    'en' => 'Food & Drink'
                ])
            ]
        ];
    }

}
