<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Review\IndexRequest;
use App\Http\Requests\Admin\Review\UpdateRequest;
use App\Http\Resources\Admin\ReviewResource;
use App\Models\Review;
use App\Services\Admin\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function index(IndexRequest $request): JsonResponse
    {
        return response()->json(
            (new ReviewService())->index($request->validated())
        );
    }

    public function create(): JsonResponse
    {
        throw new \Exception('not implemented');
    }

    public function store(): ReviewResource
    {
        throw new \Exception('not implemented');
    }

    public function show(Review $review): ReviewResource
    {
        return new ReviewResource($review->load('booking'));
    }

    public function edit(Review $review): ReviewResource
    {
        throw new \Exception('not implemented');
    }

    public function update(UpdateRequest $request, Review $review): ReviewResource
    {
        (new ReviewService())->update($request->validated(), $review);

        return new ReviewResource($review->load('booking'));
    }

    public function destroy(Review $review): JsonResponse
    {
        $review->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
