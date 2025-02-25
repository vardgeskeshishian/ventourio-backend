<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Import\CitiesInformationImportService;
use App\Services\Parsing\CitiesInformationParsingService;
use Illuminate\Http\JsonResponse;
use function response;

final class CitiesInformationParsingController extends Controller
{

    public function parseCitiesInformation(): JsonResponse
    {
        try{

            (new CitiesInformationParsingService())
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
        (new CitiesInformationImportService())->run();

        return response()->json([
            'success' => true,
            'message' => 'Import started'
        ]);
    }
}
