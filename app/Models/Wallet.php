<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    /**
     * متد کمکی برای شارژ کیف پول
     */
    public function deposit($amount, $type = 'deposit', $description = null, $status = 'confirmed', $refId = null)
    {
        $this->increment('balance', $amount);
        
        return $this->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'status' => $status,
            'description' => $description,
            'reference_id' => $refId,
            'service_name' => $this->service_name
        ]);
    }

    /**
     * متد کمکی برای برداشت از کیف پول
     */
    public function withdraw($amount, $type = 'withdraw', $description = null, $status = 'confirmed', $refId = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('موجودی ناکافی است.');
        }

        $this->decrement('balance', $amount);

        return $this->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'status' => $status,
            'description' => $description,
            'reference_id' => $refId,
            'service_name' => $this->service_name
        ]);
    }
}