<?php

namespace App\Observers;

use App\Enums\BalanceChangeType;
use App\Models\BalanceChange;
use Illuminate\Support\Facades\DB;

class BalanceChangeObserver
{
    /**
     * Handle the BalanceChange "created" event.
     *
     * @param BalanceChange $balanceChange
     * @return void
     */
    public function created(BalanceChange $balanceChange): void
    {
        if ($balanceChange->type === BalanceChangeType::ADD) {
            $this->increment($balanceChange);
        } else {
            $this->decrement($balanceChange);
        }
    }

    /**
     * Handle the BalanceChange "deleting" event.
     *
     * @param BalanceChange $balanceChange
     * @return void
     */
    public function deleting(BalanceChange $balanceChange): void
    {
        if ($balanceChange->type === BalanceChangeType::ADD) {
            $this->decrement($balanceChange);
        } else {
            $this->increment($balanceChange);
        }
    }

    private function increment(BalanceChange $balanceChange): void
    {
        DB::table('users')
            ->where('id', $balanceChange->user_id)
            ->increment('balance', $balanceChange->amount);
    }

    private function decrement(BalanceChange $balanceChange): void
    {
        DB::table('users')
            ->where('id', $balanceChange->user_id)
            ->decrement('balance', $balanceChange->amount);
    }
}
