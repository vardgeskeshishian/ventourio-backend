<?php

namespace App\Services\Web;

use App\Exceptions\BusinessException;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\User;

final class ReviewService extends WebService
{
    public function store(array $data, User $user): Review
    {
        $isCorrectBooking = Booking::where('id', $data['booking_id'])
            ->where('user_id', $user->id)
            ->exists();

        if ( ! $isCorrectBooking) {
            throw new BusinessException(__('errors.system.reload_page'));
        }

        return Review::create([
            'booking_id' => $data['booking_id'],
            'body' => $data['body'],
            'rating' => $data['rating'],
            'rating_avg' => ceil(array_sum($data['rating']) / count($data['rating'])),
        ]);
    }

    public function storeByHotel(array $data, User $user, Hotel $hotel): Review
    {
        $booking = Booking::active($user->id, $hotel->id)->latest()->first();

        if ( ! $booking) {
            throw new BusinessException(__('errors.system.reload_page'));
        }

        $data['booking_id'] = $booking->id;

        return $this->store($data, $user);
    }
}
