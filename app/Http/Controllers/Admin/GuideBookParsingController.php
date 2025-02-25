<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Parsing\GuideBookParsingService;
use App\Services\Parsing\ImportService;
use App\Services\Parsing\ParsingService;
use Illuminate\Http\JsonResponse;
use function response;

final class GuideBookParsingController extends Controller
{

    public function slider(): JsonResponse
    {
        try{

            (new GuideBookParsingService())
                        ->slider();

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

    public function latestArticles(): JsonResponse
    {
        try{

            (new GuideBookParsingService())
                        ->latestArticles();

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
