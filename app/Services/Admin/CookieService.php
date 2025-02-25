<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\CookieResource;
use App\Models\Cookie;

class CookieService
{

    public function __construct()
    {

    }

    /**
     * @param array $data
     * @return array
     */
    public function getData(array $data): array
    {
        $currencies = Cookie::withTrashed()->orderBy('id', "desc");

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $currencies->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $currencies = $currencies->take($take)->skip($skip);
        } else {
            $currencies = $currencies->take($take)->skip(0);
        }

        return [
            'data' => $currencies->get(),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }


    /**
     * @param $id
     * @return array
     */
    public function restore($id)
    {
        $currency = Cookie::withTrashed()->findOrFail($id);
        $currency->restore();
        return [
            'status'   => true,
            'data' => new CookieResource($currency),
            'message'  => 'Cookie been restored successfully!'
        ];
    }


    /**
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        $currency = Cookie::withTrashed()->findOrFail($id);
        $deleteType = null;

        if(!$currency->trashed()){
            $currency->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $currency->forceDelete();
        }

        return [
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Cookie has been deleted successfully!'
        ];
    }

}
