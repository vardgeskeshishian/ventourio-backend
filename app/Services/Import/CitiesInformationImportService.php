<?php

namespace App\Services\Import;
use App\Models\City;
use App\Traits\InteractWithParsing;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

set_time_limit(9000);


final class CitiesInformationImportService
{
    use InteractWithParsing;

    public function run(): void
    {

        $file = $this->citiesInformationMainPath();

        if ( ! file_exists($file)) {
            throw new FileNotFoundException();
        }

        $fileToJson = json_decode(file_get_contents($file), true);

        if (empty($fileToJson)) {
            throw new \Exception('ImportData File is empty');
        }
        DB::beginTransaction();
        try {
            foreach ($fileToJson as $data) {

                $city = City::where('parsing_source', $data['parsing_source'])->first();

                if($city){

                    $city->update([
                        'description_l' => $data['description'],
                        'geography_l' => $data['geography'],
                        'article_l' => $data['long_description'],
                    ]);

                    $this->attachMedia($city, $data['gallery']);
                    $this->attachMedia($city, $data['slider_images'], 'slider');
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
