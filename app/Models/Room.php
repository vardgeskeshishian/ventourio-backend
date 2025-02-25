<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_base_id',
    ];

    protected $with = [
        'roomBase'
    ];

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->roomBase->title ?? ''
        );
    }

    protected function basis(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->roomBase->basis->value ?? ''
        );
    }

    public function roomBase(): BelongsTo
    {
        return $this->belongsTo(RoomBase::class);
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class);
    }

    public function scopeAvailable(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereDoesntHave('bookings', function (Builder $query) use ($from, $to) {

            $query->whereBetween('arrival_date', [$from->startOfDay(), $to->endOfDay()])
                ->orWhereBetween('departure_date', [$from->startOfDay(), $to->endOfDay()])
                ->whereNotIn('bookings.status', BookingStatus::closed());
        });
    }
}
