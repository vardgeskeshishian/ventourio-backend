<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\CurrencyResource;
use App\Models\Currency;

class CurrencyService
{

    public function __construct()
    {

    }

    /**
     * @param $request
     * @return array
     */
    public function getData($request)
    {
        $currencies = Currency::withTrashed()->orderBy('is_main', "desc")->orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $currencies->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $currencies = $currencies->take($take)->skip($skip);
        } else {
            $currencies = $currencies->take($take)->skip(0);
        }

        return [
            'data' => CurrencyResource::collection($currencies->get()),
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
        $currency = Currency::withTrashed()->findOrFail($id);
        $currency->restore();
        return [
            'status'   => true,
            'data' => new CurrencyResource($currency),
            'message'  => 'Currency been restored successfully!'
        ];
    }


    /**
     * @param $id
     * @return array
     */
    public function setIsMain($id)
    {
        $currency = Currency::findOrFail($id);
        Currency::where('is_main', true)->update(['is_main' => false]);

        $currency->is_main = true;
        $currency->save();

        return [
            'status' => true,
            'message' => $currency->name . ' is set main currency successfully!'
        ];
    }


    /**
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        $currency = Currency::withTrashed()->findOrFail($id);
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
            'message' => 'Currency has been deleted successfully!'
        ];
    }

}
