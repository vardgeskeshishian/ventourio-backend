<?php

namespace App\Http\Controllers\Web;

use App\Enums\RatingCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Review\StoreByHotelRequest;
use App\Http\Requests\Web\Review\StoreRequest;
use App\Http\Resources\Web\ReviewResource;
use App\Models\Hotel;
use App\Services\Web\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function store(StoreRequest $request): ReviewResource
    {
        return new ReviewResource(
            (new ReviewService())->store($request->validated(), auth()->user() ?? auth('sanctum')->user())
        );
    }

    public function storeByHotel(StoreByHotelRequest $request, Hotel $hotel): ReviewResource
    {
        return new ReviewResource(
            (new ReviewService())->storeByHotel($request->validated(), auth()->user() ?? auth('sanctum')->user(), $hotel)
        );
    }

    public function getRatingCategories(): JsonResponse
    {
        $result = [];

        foreach (RatingCategory::cases() as $ratingCategory) {
            $result[] = [
                'title' => Str::lower($ratingCategory->name),
                'value' => $ratingCategory->value
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
