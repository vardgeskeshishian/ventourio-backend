<?php

namespace App\Services\Web\Hotel;

use App\Models\Hotel;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

final class ReviewService
{
    #[ArrayShape(['reviews' => "\Illuminate\Support\Collection", 'next_cursor' => "null|string"])]
    public function index(array $data, Hotel $hotel): array
    {
        $reviewsQuery = Review::query()
            ->orderByDesc('created_at')
            ->select([
                'reviews.id',
                'reviews.booking_id',
                'reviews.body',
                'reviews.rating',
                'reviews.rating_avg',
                'reviews.created_at',
            ])
            ->with([
                'booking' => function ($query) {
                    $query
                        ->select([
                            'bookings.id',
                            'bookings.user_id'
                        ])
                        ->with([
                            'user' => function ($query) {
                                $query
                                    ->select([
                                        'users.id',
                                        'users.first_name'
                                    ])
                                    ->with('media');
                            }
                        ]);
                }
            ]);

        $reviewsQuery->whereHas('booking', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        });

        $paginator = $reviewsQuery->cursorPaginate(perPage: 15, cursor: $data['cursor'] ?? null);

        $reviews = $paginator->getCollection();

        self::format($reviews);

        $nextPageUrl = $paginator->nextPageUrl();
        if ($nextPageUrl) {
            $cursor = Str::before(Str::after($nextPageUrl, 'cursor='), '&');
        }

        return [
            'reviews' => $reviews,
            'next_cursor' => $cursor ?? null
        ];
    }

    public static function format(Collection $reviews): void
    {
        /** @var Review $review */
        foreach ($reviews as $review) {
            $review->setAttribute('date', Carbon::create($review->created_at)->translatedFormat('F Y'));
            $review->booking->user->append('avatar')->makeHidden('media');
        }
    }
}
