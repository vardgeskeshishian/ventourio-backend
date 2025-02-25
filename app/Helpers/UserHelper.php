<?php

namespace App\Helpers;

use App\Enums\BalanceChangeType;
use App\Models\BalanceChange;
use App\Models\System\InteractsWithUserBalance;
use App\Models\User;

final class UserHelper
{
    public static function hasEnoughBalance(float $amountInBase, User|int $user, float &$difference = null): bool
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        $difference = $amountInBase - User::find($user, ['balance'])->balance;

        if ($difference > 0) {
            return false;
        }

        return true;
    }

    public static function increaseBalance(InteractsWithUserBalance $model): void
    {
        BalanceChange::create([
            'user_id' => $model->getUserId(),
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->getId(),
            'amount' => $model->getAmount(),
            'type' => BalanceChangeType::ADD,
        ]);
    }

    public static function decreaseBalance(InteractsWithUserBalance $model): void
    {
        BalanceChange::create([
            'user_id' => $model->getUserId(),
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->getId(),
            'amount' => $model->getAmount(),
            'type' => BalanceChangeType::SUBTRACT,
        ]);
    }
}
