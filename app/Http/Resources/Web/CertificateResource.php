<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\Admin\CurrencyResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Certificate */
class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'base_certificate_id' => $this->base_certificate_id,
            'bought_by_user_id'   => $this->bought_by_user_id,
            'amount'              => $this->amount,
            'amount_title'        => $this->amount_title,
            'currency_id'         => $this->currency_id,
            'code'                => $this->code,
            'is_paid'             => $this->is_paid,
            'paid_at'             => $this->paid_at,
            'is_used'             => $this->is_used,
            'used_at'             => $this->used_at,
            'used_by_user_id'     => $this->used_by_user_id,

            'bought_by_user'      => new UserResource($this->whenLoaded('boughtByUser')),
            'used_by_user'        => new UserResource($this->whenLoaded('usedByUser')),
            'currency'            => new CurrencyResource($this->whenLoaded('currency')),
            'base_certificate'    => new BaseCertificateResource($this->whenLoaded('baseCertificate'))
        ];
    }
}
