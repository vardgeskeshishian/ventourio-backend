<?php

namespace App\Services\Web\Filter;

use App\Services\Web\WebService;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class RoomBaseFilterService extends WebService implements FilterServiceInterface
{
    private Builder $roomBases;

    public function __construct(private readonly array $filterData)
    {
        parent::__construct();
    }

    public function filter(Builder &$builder)
    {
        if (empty($this->filterData)) {
            return null;
        }

        $this->roomBases = &$builder;

        $this->filterPeople();
        $this->filterPeopleByRooms();
        $this->filterRooms();
        $this->filterBasis();
        $this->filterPrices();
    }

    private function filterPeople()
    {
        $adults = $this->filterData['adults'] ?? null;
        $children = $this->filterData['children'] ?? null;
        if (empty($adults) && empty($children)) {
            return;
        }

        $this->roomBases->where(function(Builder $query) use ($adults, $children) {
            $query->where('children_count', '>=', $children);
            $query->where('adults_count', '>=', $adults);
        });
    }

    private function filterPeopleByRooms()
    {
        $rooms = $this->filterData['rooms'] ?? null;
        if (empty($rooms)) {
            return;
        }

        $this->roomBases->where(function (Builder $query) use ($rooms) {

            $counter = 0;
            foreach ($rooms as $room) {

                $function = function ($query) use ($room) {
                    $query->where('children_count', '>=', $room['children'] ?? 0);
                    $query->where('adults_count', '>=', $room['adults']);
                };

                if ($counter === 0) {
                    $query->where($function);
                } else {
                    $query->orWhere($function);
                }

                $counter++;
            }
        });
    }

    private function filterRooms()
    {
        $dates = $this->filterData['dates'] ?? null;
        if (empty($dates) && empty($rooms)) {
            return;
        }

        $roomFilter = new RoomFilterService([
            'dates' => $dates,
        ]);

        $this->roomBases->whereHas('rooms', function ($query) use ($roomFilter) {
            $roomFilter->filter($query);
        });
    }

    private function filterBasis()
    {
        $roomBasises = $this->filterData['room_basis'] ?? null;
        if (empty($roomBasises)) {
            return;
        }

        $this->roomBases->whereIn('basis', $roomBasises);

    }

    private function filterPrices()
    {
        $prices = $this->filterData['prices'] ?? null;
        if (empty($prices)) {
            return;
        }

        $minPrice = $prices['min'] ?? 0;
        $maxPrice = $prices['max'] ?? 99999;

        $this->roomBases->whereBetween('price', [$minPrice, $maxPrice]);
    }
}
