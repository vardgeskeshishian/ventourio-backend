<?php

namespace App\Models;

use App\Enums\RoomBasis;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class RoomBase extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'room_type_id',
        'title_l',
        'basis',
        'booking_max_term', # максимальный срок бронирования (количество дней)
        'booking_range', # дальность бронирования (количество дней) (какую максимальную дату заезда пользователь может выбрать (через год, два и тд))
        'cancel_range', # дальность отмены брони (количество дней до брони, после которого нельзя оформить возврат)
        'refundable',
        'remark_l',
        'adults_count',
        'children_count',
        'price',
        'base_price',
        'discount_id',
    ];

    protected $casts = [
        'basis' => RoomBasis::class
    ];

    public array $translatable = [
        'title_l',
        'remark_l'
    ];

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('title_l')
        );
    }

    protected function remark(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('remark_l')
        );
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }
}
