<?php

namespace Database\Seeders;

use App\Models\CompanyService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class CompanyServiceSeeder extends Seeder
{
    public function run()
    {
        $files = Storage::disk('public')->allFiles('service_fake_media');

        CompanyService::factory(5)->create()->each(function (CompanyService $service) use ($files) {

            if (count($files)) {
                $randomImagePath = Storage::disk('public')->path(Arr::random($files));
                $service->copyMedia($randomImagePath)->toMediaCollection('icon');
            }
        });
    }
}
