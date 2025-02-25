<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Models\Hotel;
use App\Http\Resources\Admin\HotelResource;
use App\Services\Web\WebService;
use Illuminate\Support\Facades\DB;

class HotelService  extends WebService
{
    public function getData($request)
    {
        $hotels = Hotel::orderBy('id', "desc");

        if ( ! empty($request->search)) {
            $hotels->whereRaw("LOWER(title_l->'$.{$this->locale}') like ?", '%'.strtolower($request->search).'%');
            $hotels->orWhereRaw("LOWER(address) like ?", '%'.strtolower($request->search).'%');
            $hotels->orWhereRaw("LOWER(id) like ?", '%'.strtolower($request->search).'%');
            $hotels->orWhereRaw("LOWER(fax) like ?", '%'.strtolower($request->search).'%');
            $hotels->orWhereRaw("LOWER(phone) like ?", '%'.strtolower($request->search).'%');
        }

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $hotels->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $hotels = $hotels->take($take)->skip($skip);
        } else {
            $hotels = $hotels->take($take)->skip(0);
        }

        return [
            'data' => HotelResource::collection($hotels->with('media')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): Hotel
    {
        DB::beginTransaction();
        try {

            $hotel = Hotel::create([
                'title_l' => $data['title_l'],
                'district_id' => $data['district_id'],
                'address' => $data['address'],
                'external_code' => $data['external_code'] ?? null,
                'description_l' => $data['description_l'] ?? null,
                'phone' => $data['phone'] ?? null,
                'fax' => $data['fax'] ?? null,
                'stars' => $data['stars'] ?? null,
                'geo' => $data['geo'] ?? null,
                'house_rules' => $data['house_rules'] ?? null,
                'is_apartment' => $data['is_apartment'] ?? null,
                'giata_code' => $data['giata_code'] ?? null,
                'discount_id' => $data['discount_id'] ?? null
            ]);

            if ( ! empty($data['media'])) {
                $hotel->addMultipleMediaFromRequest(['media'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection();
                    });
            }

            $hotel->facilities()->sync($data['facilities'] ?? []);

            $hotel->notifySubscribers();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $hotel;
    }

    public function update(array $data, Hotel $hotel): Hotel
    {
        DB::beginTransaction();
        try {

            $hotel->update([
                'title_l' => $data['title_l'],
                'district_id' => $data['district_id'],
                'address' => $data['address'],
                'external_code' => $data['external_code'] ?? null,
                'description_l' => $data['description_l'] ?? null,
                'phone' => $data['phone'] ?? null,
                'fax' => $data['fax'] ?? null,
                'stars' => $data['stars'] ?? null,
                'geo' => $data['geo'] ?? null,
                'house_rules' => $data['house_rules'] ?? null,
                'is_apartment' => $data['is_apartment'] ?? null,
                'giata_code' => $data['giata_code'] ?? null,
                'discount_id' => $data['discount_id'] ?? null
            ]);

            if ( ! empty($data['media'])) {
                $hotel->addMultipleMediaFromRequest(['media'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection();
                    });
            }

            $hotel->facilities()->sync($data['facilities'] ?? []);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $hotel;
    }
}
