<?php

namespace App\Services\Import;
use App\Models\City;
use App\Models\Country;
use App\Traits\InteractWithParsing;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

set_time_limit(9000);


final class ImportMissingFlagsService
{
    use InteractWithParsing;

    private array $flags = [
        '/en/togo'      => 'Togo.png',
        '/en/ghana'     => 'Ghana.png',
        '/en/guinea'    => 'Guinea.png',
        '/en/gambia'    => 'Gambia.png',
        '/en/gabon'     => 'Gabon.png',
        '/en/lesotho'   => 'Lesotho.png',
        '/en/nigeria'   => 'Nigeria.png',
        '/en/botswana'  => 'Botswana.png',
        '/en/kiribati'  => 'Kiribati.png',
        '/en/swaziland' => 'Swaziland.png',
        '/en/mauritania'   => 'Mauritania.png',
        '/en/montserrat'   => 'Montserrat.png',
        '/en/ivory-coast'  => 'Ivory Coast.png',
        '/en/sierra-leone' => 'Sierra Leone.png',
        '/en/sao-tome-and-principe' => 'São Tomé and Príncipe.png',
    ];

    public function run(): void
    {
        foreach($this->flags as $key => $flag){

            $country = Country::where('parsing_source', $key)->first();

            if($country){

                $path = storage_path("app/public/missing_flags/{$flag}");
                if(File::exists($path)){
                    $country->clearMediaCollection('flag');
                    $country->copyMedia($path)->toMediaCollection('flag');
                }

            }
        }
    }
}
