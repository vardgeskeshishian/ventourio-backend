<?php

namespace App\Models;

use App\Enums\ExternalPaymentMethodType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalPaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'type',
        'credit_card_id'
    ];

    protected $casts = [
        'type' => ExternalPaymentMethodType::class
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
    public function creditCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class);
    }
}
