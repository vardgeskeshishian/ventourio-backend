<?php

namespace App\Http\Controllers\Web;

use App\Enums\SortOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Hotel\BestDealsRequest;
use App\Http\Requests\Web\Hotel\FavoritesRequest;
use App\Http\Requests\Web\Hotel\GetFiltersRequest;
use App\Http\Requests\Web\Hotel\GetRequest;
use App\Http\Requests\Web\Hotel\ReviewsRequest;
use App\Http\Requests\Web\Hotel\SearchRequest;
use App\Models\Hotel;
use App\Services\Web\Hotel\BestDealsService;
use App\Services\Web\Hotel\FavoritesService;
use App\Services\Web\Hotel\FilterValuesService;
use App\Services\Web\Hotel\GetHotelService;
use App\Services\Web\Hotel\SearchService;
use App\Services\Web\PageService;
use App\Services\Web\Hotel\ReviewService;
use App\Services\Web\UserHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    public function get(GetRequest $request, string $hotelSlug)
    {
        return response()->json([
            'success' => true,
            'data' => $result = (new GetHotelService())->get(array_merge($request->validated(), ['slug' => $hotelSlug])),
            'can_write_review' => UserHelper::hasHotelBooking(auth()->user() ?? auth('sanctum')->user(), $result['id']),
            'request' => $request->all(),
        ]);
    }

    public function bestDeals(BestDealsRequest $request): JsonResponse
    {
        $result = (new BestDealsService())->getInfoForBestDeals($request->validated());

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function favorites(FavoritesRequest $request): JsonResponse
    {
        $result = (new FavoritesService())->get(auth()->user());

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function search(SearchRequest $request): JsonResponse
    {
        $result = (new SearchService())->search($request->toDto());

        return response()->json([
            'success' => true,
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
            'total' => $result->total(),
            'filterParams' => $request->validated(),
            'data' => $result->items(),
            'page' => $request->validated('with_page')
                ? (new PageService())->getForHotelSearch($request->validated('city_slug'), $request->validated('district_slug'), $request->validated('region_slug'))
                : null,
        ]);
    }

    public function getFilters(GetFiltersRequest $request): JsonResponse
    {
        $result = (new FilterValuesService())->get($request->validated());

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function getSorts(): JsonResponse
    {
        $result = [];

        foreach (SortOrder::cases() as $sortOrder) {
            $result[] = [
                'title' => Str::lower($sortOrder->name),
                'value' => $sortOrder->value
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function reviews(ReviewsRequest $request, Hotel $hotel): JsonResponse
    {
        $result = (new ReviewService())->index($request->validated(), $hotel);
        return response()->json([
            'success' => true,
            'next_cursor' => $result['next_cursor'],
            'data' => $result['reviews'],
        ]);
    }
}
