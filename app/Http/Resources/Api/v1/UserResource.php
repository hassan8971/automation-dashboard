<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 1. Determine the status/next_action
        $nextAction = 'dashboard';
        if (empty($this->name)) {
            $nextAction = 'register_name';
        } elseif (empty($this->email)) {
            $nextAction = 'register_email';
        }

        // 2. Return clean data
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'profile_photo_url' => $this->profile_photo_url, // if you use jetstream or have this accessor
            'next_action' => $nextAction, // logic is now centralized here
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}