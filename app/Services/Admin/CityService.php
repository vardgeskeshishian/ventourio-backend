<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Http\Resources\Admin\CityResource;
use App\Models\City;
use App\Services\Web\WebService;
use Illuminate\Support\Facades\DB;

class CityService extends WebService
{
    public function getData($request)
    {
        $cities = City::orderBy('id', "desc");

        if ( ! empty($request->search)) {
            $cities->whereRaw("LOWER(title_l->'$.{$this->locale}') like ?", '%'.strtolower($request->search).'%');
        }

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $cities->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $cities = $cities->take($take)->skip($skip);
        } else {
            $cities = $cities->take($take)->skip(0);
        }

        return [
            'data' => CityResource::collection($cities->with('region')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data) : City
    {
        DB::beginTransaction();
        try {
            $city = City::create([
                'title_l' => $data['title_l'],
                'geography_l' => $data['geography_l'],
                'description_l' => $data['description_l'],
                'article_l' => $data['article_l'],
                'show_in_best_deals' => $data['show_in_best_deals'],
                'region_id' => $data['region_id'],
                'external_code' => $data['external_code'],
                'geo' => $data['geo'],
            ]);

            if ( ! empty($data['media'])) {
                $city->addMultipleMediaFromRequest(['media'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection();
                    });
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $city->load([
            'media' => function ($query) {
                $query->where('collection_name', 'default');
            },
        ]);
    }


    public function update(array $data, City $city): City
    {
        DB::beginTransaction();
        try {
            $city->update([
                'title_l' => $data['title_l'],
                'geography_l' => $data['geography_l'],
                'description_l' => $data['description_l'],
                'article_l' => $data['article_l'],
                'show_in_best_deals' => $data['show_in_best_deals'],
                'region_id' => $data['region_id'],
                'external_code' => $data['external_code'],
                'geo' => $data['geo'],
            ]);

            if ( ! empty($data['media'])) {
                $city->addMultipleMediaFromRequest(['media'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection();
                    });
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $city->load([
            'media' => function ($query) {
                $query->where('collection_name', 'default');
            },
        ]);
    }

}
