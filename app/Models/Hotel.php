<?php

namespace App\Models;

use App\Models\System\HasSubscribers;
use App\Models\System\CanNotifySubscribers;
use App\Services\Web\District\QueryHelper;
use App\Services\Web\Hotel\Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Hotel extends Model implements HasMedia, HasSubscribers
{
    use HasFactory, InteractsWithMedia, HasTranslations, CanNotifySubscribers;

    protected string $mailTemplate = 'emails.subscriptions.hotel';
    protected string $mailSubject = 'New Hotel on Ventourio';

    protected $fillable = [
        'external_code',
        'district_id',
        'title_l',
        'description_l',
        'address',
        'phone',
        'fax',
        'stars',
        'geo', // Longitude, Latitude
        'is_apartment',
        'giata_code',
        'house_rules',
        'discount_id'
    ];

    protected $casts = [
        'geo' => 'array',
        'house_rules' => 'array',
        'is_apartment' => 'bool',
    ];

    public array $translatable = [
        'title_l',
        'description_l',
    ];

    protected function breadcrumbs(): Attribute
    {
        return new Attribute(
            get: fn() => Helper::breadcrumbs($this)
        );
    }

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('title_l')
        );
    }

    protected function description(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('description_l')
        );
    }

    protected function location(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => $this->district->city->title . ', ' . $this->district->city->region->country->title
        );
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Booking::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function roomBases(): hasManyThrough
    {
        return $this->hasManyThrough(RoomBase::class, RoomType::class);
    }

    public function rooms(): HasManyThrough
    {
        return $this->hasManyThrough(Room::class, RoomBase::class);
    }

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'instance');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function getMailData(): array
    {
        $this->load([
            'district' => QueryHelper::relationForBreadcrumbs(app()->getLocale())
        ]);

        return [
            'href' => config('front.web_url') . "/" // todo
        ];
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('original')->format('webp')->nonQueued();
    }
}
