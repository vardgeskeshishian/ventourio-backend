<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\GoGlobal\ImportHotelsRequest;
use App\Jobs\UpdateHotelByGoGlobalJob;
use App\Services\GoGlobal\ImportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\FileDoesNotExistException;

class GoGlobalController extends Controller
{
    /**
     * @throws FileDoesNotExistException
     * @throws Exception
     */
    public function importHotels(ImportHotelsRequest $request): JsonResponse
    {
        $result = (new ImportService())->hotels();

        return response()->json($result);
    }

    public function updateHotels(): JsonResponse
    {
        $jobs = 10;
        foreach (range(0, $jobs) as $int) {
            UpdateHotelByGoGlobalJob::dispatch(locale: 'en', offset: $int, step: $jobs + 1);
        }

        return response()->json([
            'success' => true,
            'message' => 'Updating started'
        ]);
    }

    /**
     * @throws FileDoesNotExistException
     * @throws Exception
     */
    public function importCountriesAndCities(): JsonResponse
    {
        $result = (new ImportService())->countriesAndCities();

        return response()->json($result);
    }

    public function attachCountriesAndCities(): JsonResponse
    {
        $result = (new ImportService())->attachCountriesWithCitiesToDb();

        return response()->json($result);
    }
}
