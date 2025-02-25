<?php

namespace App\Http\Resources\Admin;

use App\Models\PaymentRequisite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PaymentRequisite */
class PaymentRequisiteResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'data' => $this->data,
            'is_active' => $this->is_active
        ];
    }
}
