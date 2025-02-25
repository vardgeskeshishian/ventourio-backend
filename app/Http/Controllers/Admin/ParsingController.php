<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Parsing\ImportService;
use App\Services\Parsing\ParsingService;
use Illuminate\Http\JsonResponse;
use function response;

final class ParsingController extends Controller
{

    public function atOnce(): JsonResponse
    {
        try{

            (new ParsingService())
                        ->parseAtOnceWithoutTranslations();

            return response()->json([
                'success' => true,
                'message' => 'Success',
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
        (new ImportService())->run();

        return response()->json([
            'success' => true,
            'message' => 'Import started'
        ]);
    }
}
