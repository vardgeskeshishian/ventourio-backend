<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Http\Resources\Admin\CountryResource;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CountryService
{

    public function __construct()
    {

    }

    public function getData($request)
    {
        $countries = Country::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $countries->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $countries = $countries->take($take)->skip($skip);
        } else {
            $countries = $countries->take($take)->skip(0);
        }

        return [
            'data' => CountryResource::collection($countries->with('continent')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data) : Country
    {
        DB::beginTransaction();
        try {
            $country = Country::create([
                'title_l' => $data['title_l'],
                'geography_l' => $data['geography_l'],
                'description_l' => $data['description_l'],
                'article_l' => $data['article_l'],
                'nationality_l' => $data['nationality_l'],
                'continent_id' => $data['continent_id'],
                'iso_code' => $data['iso_code'],
                'external_code' => $data['external_code'],
                'geo' => $data['geo'],
            ]);

            if ( ! empty($data['media'])) {
                $country->addMultipleMediaFromRequest(['media'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection();
                    });
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $country->load([
            'media' => function ($query) {
                $query->where('collection_name', 'default');
            },
        ]);
    }


    public function update(array $data, Country $country): Country
    {
        DB::beginTransaction();
        try {
            $country->update([
                'title_l' => $data['title_l'],
                'geography_l' => $data['geography_l'],
                'description_l' => $data['description_l'],
                'article_l' => $data['article_l'],
                'nationality_l' => $data['nationality_l'],
                'continent_id' => $data['continent_id'],
                'iso_code' => $data['iso_code'],
                'external_code' => $data['external_code'],
                'geo' => $data['geo'],
            ]);

            if ( ! empty($data['media'])) {
                $country->addMultipleMediaFromRequest(['media'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection();
                    });
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $country->load([
            'media' => function ($query) {
                $query->where('collection_name', 'default');
            },
        ]);
    }


    /**
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        return [];
    }

}
