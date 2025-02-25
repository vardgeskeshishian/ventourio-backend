<?php

namespace App\Services\Admin;

use App\Enums\CreditCardType;
use App\Http\Resources\Admin\CreditCardResource;
use App\Models\CreditCard;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

final class CreditCardService
{
    public function index(array $data): array
    {
        $creditCards = CreditCard::query();

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $creditCards->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $creditCards = $creditCards->take($take)->skip($skip);
        } else {
            $creditCards = $creditCards->take($take)->skip(0);
        }

        return [
            'success' => true,
            'data' => CreditCardResource::collection($creditCards->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): CreditCard
    {
        $type = CreditCardType::from($data['type'] ?? null);

        $number = Str::slug($data['number'], '');
        $title = '************' . substr($number, 12);

        return CreditCard::create([
            'title' => $title,
            'holder_name' => $data['holder_name'],
            'type' => $type,
            'number' => Crypt::encryptString($number),
            'exp_month' => Crypt::encryptString($data['exp_month']),
            'exp_year' => Crypt::encryptString($data['exp_year']),
            'svc' => Crypt::encryptString($data['svc'])
        ]);
    }
}
