<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? $this->name, // Assuming 'title' or 'name' based on your model
            'price' => (int) $this->price,
            'formatted_price' => number_format($this->price) . ' تومان',
            'description' => $this->description,
            'type' => $this->type, // If addons have types (e.g. 'speed_boost', 'extra_storage')
        ];
    }
}