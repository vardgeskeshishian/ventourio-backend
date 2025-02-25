<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\PaymentRequisiteResource;
use App\Models\PaymentRequisite;

final class PaymentRequisiteService
{
    public function index(array $data): array
    {
        $paymentRequisites = PaymentRequisite::query();

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $paymentRequisites->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $paymentRequisites = $paymentRequisites->take($take)->skip($skip);
        } else {
            $paymentRequisites = $paymentRequisites->take($take)->skip(0);
        }

        return [
            'success' => true,
            'data' => PaymentRequisiteResource::collection($paymentRequisites->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): PaymentRequisite
    {
        return PaymentRequisite::create([
            'data' => $data['data'],
            'is_active' => $data['is_active'],
        ]);
    }

    public function update(array $data, PaymentRequisite $paymentRequisite): void
    {
        $paymentRequisite->update([
            'data' => $data['data'],
            'is_active' => $data['is_active'],
        ]);
    }
}
