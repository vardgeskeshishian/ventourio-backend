<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\ReviewResource;
use App\Models\Review;

class ReviewService
{
    public function index(array $data): array
    {
        $reviews = Review::orderBy('id', "desc");

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $reviews->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $reviews = $reviews->take($take)->skip($skip);
        } else {
            $reviews = $reviews->take($take)->skip(0);
        }

        return [
            'success' => true,
            'data' => ReviewResource::collection($reviews->with('booking')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function update(array $data, Review $review): void
    {
        $review->update([
            'body' => $data['body'],
            'rating' => $data['rating'],
            'rating_avg' => ceil(array_sum($data['rating']) / count($data['rating'])),
        ]);
    }
}
