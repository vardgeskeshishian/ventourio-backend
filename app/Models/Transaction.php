<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Helpers\CurrencyConverter;
use App\Models\System\CanChangeUserBalance;
use App\Models\System\InteractsWithUserBalance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model implements InteractsWithUserBalance
{
    use HasFactory, CanChangeUserBalance;

    protected $fillable = [
        'payment_way_id',
        'status',
        'instance_id',
        'instance_type',
        'user_id',
        'paid_at',
        'amount',
        'currency_id',
        'extra',
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
        'paid_at' => 'datetime',
        'extra' => 'array'
    ];

    public function paymentWay(): BelongsTo
    {
        return $this->belongsTo(PaymentWay::class);
    }

    public function instance(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function getAmount(): float
    {
        return CurrencyConverter::toMain($this->amount, $this->currency_id);
    }
}
