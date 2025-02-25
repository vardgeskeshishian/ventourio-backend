<?php

namespace App\Services\Web\Filter;

use App\Services\Web\WebService;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class HotelFilterService extends WebService implements FilterServiceInterface
{
    private Builder $hotels;

    public function __construct(private readonly array $filterData)
    {
        parent::__construct();
    }

    public function filter(Builder &$builder)
    {
        if (empty($this->filterData)) {
            return null;
        }

        $this->hotels = &$builder;

        $this->filterIds();
        $this->filterDistrict();
        $this->filterCity();
        $this->filterRegion();
        $this->filterRoomTypes();
        $this->filterStars();
        $this->filterDiscount();
        $this->filterExternalCodes();
    }

    private function filterIds()
    {
        $hotels = $this->filterData['hotels'] ?? null;
        if (empty($hotels)) {
            return;
        }

        $this->hotels->whereIn('id', $hotels);
    }

    private function filterCity()
    {
        $cityId = $this->filterData['city'] ?? null;
        if (empty($cityId)) {
            return;
        }

        $this->hotels->whereHas('district', function ($query) use ($cityId) {
            $query->where('city_id', $cityId);
        });
    }

    private function filterRoomTypes()
    {
        $dates = $this->filterData['dates'] ?? null;
        $rooms = $this->filterData['rooms'] ?? null;
        $roomBasises = $this->filterData['room_basis'] ?? null;
        $prices = $this->filterData['prices'] ?? null;
        if (empty($dates) && empty($rooms) && empty($roomBasises) && empty($prices)) {
            return;
        }

        $roomTypeFilter = new RoomTypeFilterService([
            'dates' => $dates,
            'rooms' => $rooms,
            'room_basis' => $roomBasises,
            'prices' => $prices
        ]);

        $this->hotels->whereHas('roomTypes', function ($query) use ($roomTypeFilter) {
            $roomTypeFilter->filter($query);
        });
    }

    private function filterStars()
    {
        $stars = $this->filterData['stars'] ?? null;
        if (empty($stars)) {
            return;
        }

        $this->hotels->where(function (Builder $query) use ($stars) {

            $query->whereIn('stars', $stars);

            if (in_array(0, $stars)) {
                $query->orWhereNull('stars');
            }
        });
    }

    private function filterDistrict()
    {
        $district = $this->filterData['district'] ?? null;
        if (empty($district)) {
            return;
        }

        if (is_array($district)) {
            $this->hotels->whereIn('district_id', $district);
        } else {
            $this->hotels->where('district_id', $district);
        }
    }

    private function filterDiscount()
    {
        $onlyWithDiscount = $this->filterData['only_discount'] ?? null;
        if (empty($onlyWithDiscount)) {
            return;
        }

        $this->hotels->whereNotNull('discount_id');
    }

    private function filterRegion()
    {
        $regionId = $this->filterData['region'] ?? null;
        if (empty($regionId)) {
            return;
        }

        $this->hotels->whereHas('district', function (Builder $query) use ($regionId) {
            $query->whereHas('city', function (Builder $query) use ($regionId) {
                $query->where('region_id', $regionId);
            });
        });
    }

    private function filterExternalCodes()
    {
        $externalCodes = $this->filterData['hotel']['external_codes'] ?? null;
        if (empty($externalCodes)) {
            return;
        }

        $this->hotels->orWhereIn('external_code', $externalCodes);
    }
}
