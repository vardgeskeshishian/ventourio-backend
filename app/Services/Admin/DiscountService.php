<?php

namespace App\Services\Admin;

use App\Enums\DiscountType;
use App\Http\Resources\Admin\DiscountResource;
use App\Models\Discount;

final class DiscountService
{
    public function index(array $data): array
    {
        $discounts = Discount::orderBy('expired_at');

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $discounts->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $discounts = $discounts->take($take)->skip($skip);
        } else {
            $discounts = $discounts->take($take)->skip(0);
        }

        return [
            'success' => true,
            'data' => DiscountResource::collection($discounts->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): Discount
    {
        return Discount::create([
            'type' => DiscountType::from($data['type']),
            'amount' => $data['amount'],
            'expired_at' => $data['expired_at'] ?? null,
        ]);
    }

    public function update(array $data, Discount $discount): void
    {
        $discount->update([
            'type' => DiscountType::from($data['type']),
            'amount' => $data['amount'],
            'expired_at' => $data['expired_at'] ?? null,
        ]);

        if ($discount->wasChanged(['type', 'amount'])) {
            $discount->updateRelationPrices();
        }
    }
}
