<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => (int) $this->price,
            'formatted_price' => number_format($this->price) . ' تومان',
            'duration_days' => $this->duration_in_days,
            'description' => $this->description,
            // 'is_active' is filtered in the controller, no need to send it
        ];
    }
}