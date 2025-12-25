<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (int) $this->amount,
            'formatted_amount' => number_format($this->amount) . ' تومان',
            'type' => $this->type, // deposit, withdraw, manual_add, etc.
            'type_label' => $this->getTypeLabel(),
            'status' => $this->status, // confirmed, pending, failed
            'status_label' => $this->status === 'confirmed' ? 'موفق' : 'ناموفق',
            'description' => $this->description,
            'date' => $this->created_at->toIso8601String(),
            'date_human' => $this->created_at->diffForHumans(),
        ];
    }

    private function getTypeLabel()
    {
        return match ($this->type) {
            'deposit', 'manual_add' => 'واریز',
            'withdraw', 'manual_sub' => 'برداشت',
            default => 'تراکنش',
        };
    }
}