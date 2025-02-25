<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Import\CountriesInformationImportService;
use App\Services\Parsing\CountriesInformationParsingService;
use Illuminate\Http\JsonResponse;
use function response;

final class CountriesInformationParsingController extends Controller
{

    public function parseCountriesInformation(): JsonResponse
    {
        try{

            (new CountriesInformationParsingService())
                        ->parse();

            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);

        }catch(\Exception $e){

            return response()->json([
                'success' => true,
                'message' => [$e->getMessage(), $e->getLine()],
            ]);

        }
    }

    public function import(): JsonResponse
    {
        (new CountriesInformationImportService())->run();

        return response()->json([
            'success' => true,
            'message' => 'Import started'
        ]);
    }
}
