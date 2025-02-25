<?php

namespace App\Http\Resources\Web;

use App\Models\Currency;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Currency */
class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'name' => $this->name,
            'code' => $this->code,
            'symbol' => $this->symbol,
            'is_main' => $this->is_main,
            'currency_rate' => $this->currency_rate,
            'status' => $this->deleted_at ? 'inactive' : 'active',
            'created_at' => $this->created_at,
        ];
    }
}
