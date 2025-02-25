<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\Provider;
use App\Models\System\CanChangeUserBalance;
use App\Models\System\InteractsWithUserBalance;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Booking extends Model implements InteractsWithUserBalance
{
    use HasFactory, CanChangeUserBalance;

    protected $fillable = [
        'hotel_id',
        'search_code',
        'external_code',
        'provider',
        'price',
        'status',
        'user_id',
        'lead_person',
        'arrival_date',
        'departure_date',
        'cancel_deadline',
        'paid_at',
        'extra'
    ];

    protected $casts = [
        'status' => BookingStatus::class,
        'extra' => 'array',
        'lead_person' => 'array',
        'provider' => Provider::class,
        'paid_at' => 'datetime',
    ];

    public function externalPaymentMethod(): HasOne
    {
        return $this->hasOne(ExternalPaymentMethod::class);
    }

    protected function isPaid(): Attribute
    {
        return new Attribute(
            get: fn() => !empty($this->paid_at)
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'instance');
    }

    public function scopeActive(Builder $query, int $userId, int $hotelId): Builder
    {
        return $query->where('user_id', $userId)
            ->where('hotel_id', $hotelId);
        // todo add booking statuses if needed
    }

    public function getAmount(): float
    {
        return $this->price;
    }
}
