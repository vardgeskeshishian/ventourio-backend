<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Http\Resources\Admin\CompanyServiceResource;
use App\Models\CompanyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyServiceService
{
    public function getData($request)
    {
        $companyServices = CompanyService::orderBy('id', "desc");

        $page  = $request->input('page') ? : 1;
        $take  = $request->input('count') ? : 8;
        $count = $companyServices->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $companyServices = $companyServices->take($take)->skip($skip);
        } else {
            $companyServices = $companyServices->take($take)->skip(0);
        }

        return [
            'data' => CompanyServiceResource::collection($companyServices->with('page')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store($request){

        $serviceData = $request->only(['title_l', 'description_l']);

        $pageData = $request->only('slug');

        DB::beginTransaction();
        try {

            $companyService = CompanyService::create($serviceData);

            $companyService->addMediaFromRequest('icon')->toMediaCollection('icon');

            $companyService->addMediaFromRequest('image')->toMediaCollection('image');

            $companyService->page()->create($pageData);

            DB::commit();

            return $companyService->load('page');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());
            throw new BusinessException(__('errors.service.create'));
        }
    }

    public function update($request, $companyService)
    {

        $serviceData = $request->only(['title_l', 'description_l']);

        $pageData = $request->only('slug');

        DB::beginTransaction();
        try {

            $companyService->update($serviceData);

            if($request->file('icon'))
            {

                $companyService->clearMediaCollection('icon');
                $companyService->addMediaFromRequest('icon')->toMediaCollection('icon');

            }
            if($request->file('image'))
            {
                $companyService->clearMediaCollection('image');
                $companyService->addMediaFromRequest('image')->toMediaCollection('image');
            }
            $companyService->page()->update($pageData);

            DB::commit();

            return $companyService->load('page');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());
            throw new BusinessException(__('errors.service.update'));

        }
    }
}
