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
            // --- Computed Status (Guest, Subscriber, etc.) ---
            'status' => $this->status, // Uses the getStatusAttribute() from User model
            // --- Metadata & Lists ---
            'metadata' => [
                'license_view_count' => (int) $this->license_view_count,
                
                // Devices List
                'devices' => $this->devices->map(function ($device) {
                    return [
                        'id' => $device->id,
                        'model' => $device->model,
                        'imei' => $device->imei,
                        'udid' => $device->udid,
                        'serial' => $device->serial,
                    ];
                }),

                // Active Subscription (Detailed)
                'active_subscription' => $this->activeSubscription ? [
                    'plan' => $this->activeSubscription->plan->name,
                    'started_at' => $this->activeSubscription->started_at->toIso8601String(),
                    'expires_at' => $this->activeSubscription->expires_at->toIso8601String(),
                    'days_left' => now()->diffInDays($this->activeSubscription->expires_at, false),
                ] : null,

                // Installed Apps (Purchases)
                'installed_apps' => $this->installedApps->map(function ($app) {
                    return [
                        'name' => $app->app_name,
                        'bundle_id' => $app->bundle_id,
                        'downloaded_at' => $app->downloaded_at ? $app->downloaded_at->toIso8601String() : null,
                    ];
                }),

                // Redeem History
                'redeem_history' => $this->redemptions->map(function ($redemption) {
                    return [
                        'code' => $redemption->code,
                        'used_at' => $redemption->used_at ? $redemption->used_at->toIso8601String() : null,
                    ];
                }),
            ],

            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}