<?php

namespace App\Services\Web;

use App\DTO\CreateTransactionDTO;
use App\Models\Transaction;

final class TransactionService extends WebService
{
    public function create(CreateTransactionDTO $dto): Transaction
    {
        return Transaction::create($dto->toArray());
    }
}
