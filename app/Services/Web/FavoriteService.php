<?php

namespace App\Services\Web;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Log;

final class FavoriteService extends WebService
{
    public function toggle($request)
    {
        try{

            auth()->user()->favorites()->toggle($request['hotel_id']);

            return auth()->user();

        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw new BusinessException($e->getMessage());
        }

    }
}
