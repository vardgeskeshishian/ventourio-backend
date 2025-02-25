<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentWay extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_system_id',
        'payment_system_way',
        'enabled',
        'settings'
    ];

    public function paymentSystem(): BelongsTo
    {
        return $this->belongsTo(PaymentSystem::class);
    }
}
