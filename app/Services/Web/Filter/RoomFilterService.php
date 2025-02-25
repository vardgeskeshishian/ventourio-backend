<?php

namespace App\Services\Web\Filter;

use App\Services\Web\WebService;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class RoomFilterService extends WebService implements FilterServiceInterface
{
    private Builder $rooms;

    public function __construct(private readonly array $filterData)
    {
        parent::__construct();
    }

    public function filter(Builder &$builder)
    {
        if (empty($this->filterData)) {
            return null;
        }

        $this->rooms = &$builder;

        $this->filterDates();
        $this->filterIds();
    }

    private function filterDates()
    {
        $dates = $this->filterData['dates'] ?? null;
        if (empty($dates)) {
            return;
        }

        /** @var Carbon $arrivalDate */
        $arrivalDate = $dates['arrival'];
        /** @var Carbon $departureDate */
        $departureDate = $dates['departure'];

        $this->rooms->available($arrivalDate, $departureDate);
    }

    private function filterIds()
    {
        $rooms = $this->filterData['ids'] ?? null;
        if (empty($rooms)) {
            return;
        }

        $this->rooms->whereIn('id', $rooms);
    }
}
