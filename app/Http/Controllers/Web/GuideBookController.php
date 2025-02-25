<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\GuideBook\IndexCitiesRequest;
use App\Http\Requests\Web\GuideBook\IndexCountriesRequest;
use App\Models\Page;
use App\Services\Web\GuideBookService;
use App\Services\Web\WebService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GuideBookController extends Controller
{
    public function index(): JsonResponse
    {
        $continents = (new GuideBookService())->index();

        return response()->json([
            'success' => true,
            'data' => $continents
        ], 200);
    }


    public function getPageBySlug(Page $page): JsonResponse
    {

        $pageBySlug = (new GuideBookService())->getPageBySlug($page);

        return response()->json([
            'data' => $pageBySlug,
            'status' => true
        ]);
    }

    public function guideBook(Request $request)
    {
        $guideData = (new GuideBookService())->getContinentCountryCity();

        $guideData['articles'] = (new GuideBookService())->latestArticles($request);

        $guideData['articlesSides'] = (new GuideBookService())->latestArticlesSlides();

        return response()->json([
            'success' => true,
            'data' => $guideData
        ], 200);

    }

    public function indexCountries(IndexCountriesRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => (new GuideBookService())->indexCountries($request->validated())
        ]);
    }

    public function indexCountriesByContinent(Page $page)
    {
        $countries = (new GuideBookService())->indexCountriesByContinent($page->slug);

        return response()->json([
            'success' => true,
            'data' => [
                'countries' => $countries
            ]
        ]);
    }

    public function indexCitiesByContinent(Page $page)
    {
        $cities = (new GuideBookService())->indexCitiesByContinent($page->slug);

        return response()->json([
            'success' => true,
            'data' => [
                'cities' => $cities
            ]
        ]);
    }

    public function indexCities(IndexCitiesRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => (new GuideBookService())->indexCities($request->validated())
        ]);
    }

    public function destinationShow(Request $request, Page $page)
    {
        $data = (new GuideBookService())->destination($request, $page);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function filterDestinationHotelsByStar(Request $request, Page $page): JsonResponse
    {
        $data = (new GuideBookService())->filterHotelsByStar($request, $page);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

}
