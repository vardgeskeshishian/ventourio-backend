<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Web\BookingResource;
use App\Models\ExternalPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ExternalPaymentMethod */
class ExternalPaymentMethodResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'booking_id' => $this->booking_id,
            'credit_card_id' => $this->credit_card_id,

            'booking' => new BookingResource($this->whenLoaded('booking')),
            'creditCard' => new CreditCardResource($this->whenLoaded('creditCard')),
        ];
    }
}
