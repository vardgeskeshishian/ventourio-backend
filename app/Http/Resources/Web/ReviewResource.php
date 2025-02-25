<?php

namespace App\Http\Resources\Web;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Review */
class ReviewResource extends JsonResource
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
            'id' =>$this->id,
            'body' => $this->body,
            'booking_id' => $this->booking_id,
            'rating' => $this->rating,
            'rating_avg' => $this->rating_avg,
            'booking' => new BookingResource($this->whenLoaded('booking')),
        ];
    }

}
