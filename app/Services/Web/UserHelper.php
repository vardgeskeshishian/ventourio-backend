<?php

namespace App\Services\Web;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Support\Collection;

final class UserHelper
{
    public static function favoriteHotelIds(null|int|User $user): Collection
    {
        if (is_int($user)) {
            $user = User::where('id', $user)
                ->with('favorites:id')
                ->first();
        }

        if ( ! $user) {
            return collect();
        }

        if ( ! $user->relationLoaded('favorites')) {
            $user->load('favorites:id');
        }

        return $user->favorites->pluck('id');
    }

    public static function hasHotelBooking(null|int|User $user, null|int|Hotel $hotel): bool
    {
        if ( ! $user || ! $hotel) {
            return false;
        }

        if ($user instanceof User) {
            $user = $user->id;
        }

        if ($hotel instanceof Hotel) {
            $hotel = $hotel->id;
        }

        return Booking::active($user, $hotel)->exists();
    }
}
