<?php

namespace App\Services\Admin;

use App\Enums\TransactionStatus;
use App\Helpers\UserHelper;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

final class TransactionService
{
    public function complete(Transaction $transaction): void
    {
        DB::beginTransaction();
        try {

            $transaction->update([
                'status' => TransactionStatus::COMPLETED,
                'paid_at' => now()
            ]);

            UserHelper::increaseBalance($transaction);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
