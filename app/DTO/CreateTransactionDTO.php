<?php

namespace App\DTO;

use App\Enums\TransactionStatus;
use Carbon\Carbon;

final class CreateTransactionDTO extends DTO
{
    public function __construct(
        private readonly int $paymentWayId,
        private readonly array $morphInstance,
        private readonly int $userId,
        private readonly float $amount,
        private readonly int $currencyId,
        private readonly ?array $extra = null,
        private readonly TransactionStatus $status = TransactionStatus::WAITING,
        private readonly ?Carbon $paidAt = null,
    ) {}

    /**
     * @return int
     */
    public function getPaymentWayId(): int
    {
        return $this->paymentWayId;
    }

    /**
     * @return array
     */
    public function getMorphInstance(): array
    {
        return $this->morphInstance;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    /**
     * @return array|null
     */
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    /**
     * @return TransactionStatus
     */
    public function getStatus(): TransactionStatus
    {
        return $this->status;
    }

    /**
     * @return Carbon|null
     */
    public function getPaidAt(): ?Carbon
    {
        return $this->paidAt;
    }

    public function toArray(): array
    {
        return [
            'payment_way_id' => $this->getPaymentWayId(),
            'status' => $this->getStatus(),
            'instance_id' => $this->getMorphInstance()['instance_id'],
            'instance_type' => $this->getMorphInstance()['instance_type'],
            'user_id' => $this->getUserId(),
            'paid_at' => $this->getPaidAt(),
            'amount' => $this->getAmount(),
            'currency_id' => $this->getCurrencyId(),
            'extra' => $this->getExtra(),
        ];
    }
}
