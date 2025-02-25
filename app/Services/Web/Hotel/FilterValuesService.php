<?php

namespace App\Services\Web\Hotel;

use App\DTO\SearchHotelsDTO;
use App\Enums\HotelStars;
use App\Enums\RoomBasis;
use App\Models\Currency;
use App\Services\Web\WebService;
use Illuminate\Support\Str;

final class FilterValuesService extends WebService
{
    public function get(array $data): array
    {
        return [
            'prices'     => $this->getPrices($data),
            'room_basis' => $this->getBasises($data),
            'stars'      => $this->getStars($data),
        ];
    }

    private function getPrices(array $data): array
    {
        $presetValues = [
            [
                'min' => 50,
                'max' => 100
            ],
            [
                'min' => 100,
                'max' => 150,
            ],
            [
                'min' => 150,
                'max' => 200,
            ],
            [
                'min' => 200,
                'max' => 250,
            ]
        ];

        /** @var Currency $currency */
        $currency = Currency::getCached()
            ->where('code', $this->currency)
            ->first();

        $searchService = new SearchService();

        $dto = new SearchHotelsDTO(
            nationality: $data['nationality'],
            regionSlug: $data['region_slug'] ?? null,
            citySlug: $data['city_slug'] ?? null,
            districtSlug: $data['district_slug'] ?? null,
            onlyDiscount: $data['only_discount'] ?? false
        );

        $result = [
            'min' => $searchService->minPrice($dto),
            'max' => $searchService->maxPrice($dto)
        ];

        foreach ($presetValues as $presetValue) {

            $countDto = new SearchHotelsDTO(
                nationality: $data['nationality'],
                regionSlug: $data['region_slug'] ?? null,
                citySlug: $data['city_slug'] ?? null,
                districtSlug: $data['district_slug'] ?? null,
                prices: $presetValue,
                onlyDiscount: $data['only_discount'] ?? false
            );

            try {
                $count = $searchService->count($countDto);
            } catch (\Exception $e) {
                continue;
            }

            $result['values'][] = [
                'title' => $currency->symbol . $presetValue['min'] . ' - ' . $currency->symbol . $presetValue['max'],
                'value' => $presetValue,
                'count' => $count
            ];
        }

        return $result;
    }

    private function getBasises(array $data): array
    {
        $roomBasises = RoomBasis::cases();

        $result = [];

        foreach ($roomBasises as $roomBasis) {

            $value = $roomBasis->value;

            $dto = new SearchHotelsDTO(
                nationality: $data['nationality'],
                regionSlug: $data['region_slug'] ?? null,
                citySlug: $data['city_slug'] ?? null,
                districtSlug: $data['district_slug'] ?? null,
                roomBasis: [$value],
                onlyDiscount: $data['only_discount'] ?? false
            );

            try {
                $count = (new SearchService())->count($dto);
            } catch (\Exception $e) {
                continue;
            }

            $result[] = [
                'title' => Str::lower($roomBasis->name),
                'value' => $value,
                'count' => $count
            ];
        }

        return $result;
    }

    private function getStars(array $data): array
    {
        $stars = HotelStars::cases();

        $result = [];

        foreach ($stars as $star) {

            $value = $star->value;

            $dto = new SearchHotelsDTO(
                nationality: $data['nationality'],
                regionSlug: $data['region_slug'] ?? null,
                citySlug: $data['city_slug'] ?? null,
                districtSlug: $data['district_slug'] ?? null,
                stars: [$value],
                onlyDiscount: $data['only_discount'] ?? false
            );

            try {
                $count = (new SearchService())->count($dto);
            } catch (\Exception $e) {
                continue;
            }

            if ($count <= 0) {
                continue;
            }

            $result[] = [
                'title' => Str::lower($star->name),
                'value' => $value,
                'count' => $count
            ];
        }

        return $result;
    }
}
