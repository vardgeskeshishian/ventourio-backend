<?php

namespace App\Http\Requests\Web\Hotel;

use App\DTO\SearchHotelsDTO;
use App\Enums\Helper;
use App\Enums\RoomBasis;
use App\Enums\SortOrder;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nationality' => 'required|string',
            'search_text' => 'nullable|string',
            'region_slug' => 'nullable|string',
            'city_slug' => 'nullable|string',
            'district_slug' => 'nullable|array',
                'district_slug.*' => 'required|string',

            'rooms' => 'nullable|array',
                'rooms.*.adults' => 'required|numeric',
                'rooms.*.children' => 'required|numeric',
            'prices' => 'nullable|array',
                'prices.min' => 'nullable|numeric|min:0',
                'prices.max' => 'nullable|numeric|min:0',
            'dates' => 'nullable|array',
                'dates.arrival' => 'required_with:dates|date',
                'dates.departure' => 'nullable|date',
            'stars' => 'nullable|array',
                'stars.*' => 'required_with:stars|numeric',

            'room_basis' => 'nullable|array',
                'room_basis.*' => 'required_with:room_basis|numeric|in:' . Helper::implode(RoomBasis::cases()),

            'sort' => 'nullable|numeric|in:' . Helper::implode(SortOrder::cases()),
            'page' => 'nullable|numeric|min:1',
            'with_page' => 'nullable|boolean',
            'only_discount' => 'nullable|boolean'
        ];
    }

    public function toDto(): SearchHotelsDTO
    {
        return new SearchHotelsDTO(
            nationality: $this->validated('nationality'),
            regionSlug: $this->validated('region_slug'),
            citySlug: $this->validated('city_slug'),
            districtSlug: $this->validated('district_slug'),
            rooms: $this->validated('rooms'),
            prices: $this->validated('prices'),
            dates: $this->validated('dates'),
            stars: $this->validated('stars'),
            roomBasis: $this->validated('room_basis'),
            sortOrder: SortOrder::tryFrom($this->validated('sort')),
            page: $this->validated('page'),
            onlyDiscount: $this->validated('only_discount')
        );
    }
}
