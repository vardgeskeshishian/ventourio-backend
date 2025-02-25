<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Http\Resources\Admin\FacilityResource;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;

final class FacilityService
{
    public function getData($request)
    {
        $facilities = Facility::query();

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $facilities->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $facilities = $facilities->take($take)->skip($skip);
        } else {
            $facilities = $facilities->take($take)->skip(0);
        }

        return [
            'data' => FacilityResource::collection($facilities->with(['media', 'category'])->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): Facility
    {
        DB::beginTransaction();
        try {

            $facility = Facility::create([
                'title_l' => $data['title_l'],
                'category_id' => $data['category_id']
            ]);

            if ( ! empty($data['image'])) {
                $facility->addMedia($data['image'])
                    ->toMediaCollection();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $facility;
    }

    public function update(array $data, Facility $facility): Facility
    {
        DB::beginTransaction();
        try {

            $facility->update([
                'title_l' => $data['title_l'],
                'category_id' => $data['category_id']
            ]);

            if ( ! empty($data['image'])) {
                $facility->clearMediaCollection();
                $facility->addMedia($data['image'])
                    ->toMediaCollection();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException($e->getMessage());
        }

        return $facility;
    }
}
