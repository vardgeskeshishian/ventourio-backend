<?php

namespace App\Http\Resources\Web;

use App\Models\BaseCertificate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BaseCertificate */
class BaseCertificateResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'amount' => $this->amount,
            'color_hex' => $this->color_hex,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
