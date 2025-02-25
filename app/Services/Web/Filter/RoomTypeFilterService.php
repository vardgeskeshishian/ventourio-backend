<?php

namespace App\Services\Web\Filter;

use App\Services\Web\WebService;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class RoomTypeFilterService extends WebService implements FilterServiceInterface
{
    private Builder $roomTypes;

    public function __construct(private readonly array $filterData)
    {
        parent::__construct();
    }

    public function filter(Builder &$builder)
    {
        if (empty($this->filterData)) {
            return null;
        }

        $this->roomTypes = &$builder;

        $this->filterCity();
        $this->filterHotels();
        $this->filterRoomBases();
    }

    private function filterCity()
    {
        $cityId = $this->filterData['city_id'] ?? null;
        if (empty($cityId)) {
            return;
        }

        $this->roomTypes->whereHas('hotel', function (Builder $query) use ($cityId) {
            $query->whereHas('district', function ($query) use ($cityId) {
                $query->where('city_id', $cityId);
            });
        });
    }

    private function filterHotels()
    {
        $hotels = $this->filterData['hotels'] ?? null;
        if (empty($hotels)) {
            return;
        }

        $this->roomTypes->whereIn('hotel_id', $hotels);
    }

    private function filterRoomBases()
    {
        $dates = $this->filterData['dates'] ?? null;
        $rooms = $this->filterData['rooms'] ?? null;
        $roomBasises = $this->filterData['room_basis'] ?? null;
        $prices = $this->filterData['prices'] ?? null;
        if (empty($dates) && empty($rooms) && empty($roomBasises) && empty($prices)) {
            return;
        }

        $roomBaseFilter = new RoomBaseFilterService([
            'dates' => $dates,
            'rooms' => $rooms,
            'room_basis' => $roomBasises,
            'prices' => $prices
        ]);

        $this->roomTypes->whereHas('roomBases', function ($query) use ($roomBaseFilter) {
            $roomBaseFilter->filter($query);
        });
    }
}
