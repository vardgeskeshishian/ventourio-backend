<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Page;
use App\Services\GoGlobal\ImportService;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Psy\Readline\Hoa\FileDoesNotExistException;

class HotelSeeder extends Seeder
{
    public function run()
    {
//        $importService = new ImportService();
//
//        try {
//            $importService->hotels(15);
//        } catch (FileDoesNotExistException|Exception $e) {
            Hotel::factory(15)->create();
//        }
    }
}
