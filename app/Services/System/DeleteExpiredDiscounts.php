<?php

namespace App\Services\System;

use App\Models\Discount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class DeleteExpiredDiscounts
{
    public function __invoke(): void
    {
        $discounts = Discount::expired()->get('id');

        DB::beginTransaction();
        try {

            foreach ($discounts as $discount) {
                $discount->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
