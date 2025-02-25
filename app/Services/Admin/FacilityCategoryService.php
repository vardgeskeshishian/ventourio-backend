<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Http\Resources\Admin\FacilityCategoryResource;
use App\Models\FacilityCategory;
use Illuminate\Support\Facades\DB;

final class FacilityCategoryService
{
    public function getData($request)
    {
        $facilitiesCategories = FacilityCategory::query();

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $facilitiesCategories->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $facilitiesCategories = $facilitiesCategories->take($take)->skip($skip);
        } else {
            $facilitiesCategories = $facilitiesCategories->take($take)->skip(0);
        }

        return [
            'data' => FacilityCategoryResource::collection($facilitiesCategories->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): FacilityCategory
    {
        DB::beginTransaction();
        try {

            $facilityCategory = FacilityCategory::create([
                'title_l' => $data['title_l'],
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $facilityCategory;
    }

    public function update(array $data, FacilityCategory $facilityCategory): FacilityCategory
    {
        DB::beginTransaction();
        try {

            $facilityCategory->update([
                'title_l' => $data['title_l'],
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $facilityCategory;
    }
}
