<?php

namespace App\Listeners;

use App\Events\HotelDiscountWasChanged;
use App\Models\RoomBase;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddHotelDiscountToRoomBases implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param HotelDiscountWasChanged $event
     * @return void
     */
    public function handle(HotelDiscountWasChanged $event): void
    {
        $hotel = $event->hotel;

        if (empty($hotel->discount_id)) {
            return;
        }

        if ( ! $hotel->relationLoaded('discount')) {
            $hotel->load('discount');
        }

        RoomBase::whereHas('roomType', function (Builder $query) use($hotel) {
            $query->where('hotel_id', $hotel->id);
        })->update([
            'discount_id' => $hotel->discount_id,
            'price' => $hotel->discount->applyRawExpression('base_price')
        ]);
    }
}
