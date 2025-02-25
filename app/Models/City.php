<?php

namespace App\Models;

use App\Services\Web\City\Helper;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToTroughTrait;

class City extends Model implements HasMedia
{
    use HasFactory, HasTranslations, BelongsToTroughTrait, InteractsWithMedia;

    protected $fillable = [
        'title_l',
        'region_id',
        'external_code',
        'show_in_best_deals',
        'geo',
        'geography_l',
        'description_l',
        'article_l',
        'parsing_source'
    ];

    protected $casts = [
        'show_in_best_deals' => 'boolean',
        'geo' => 'array',
    ];

    public array $translatable = [
        'title_l',
        'geography_l',
        'description_l',
        'article_l',
    ];

    protected $with = [
        'media'
    ];

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('title_l')
        );
    }

    protected function geography(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('geography_l')
        );
    }

    protected function description(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('description_l')
        );
    }

    protected function article(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('article_l')
        );
    }

    protected function breadcrumbs(): Attribute
    {
        return new Attribute(
            get: fn() => Helper::breadcrumbs($this)
        );
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function continent(): BelongsToThrough
    {
        return $this->belongsToThrough(Continent::class, [Country::class, Region::class]);
    }

    public function country(): BelongsToThrough
    {
        return $this->belongsToThrough(Country::class, Region::class);
    }

    public function defaultMedia(): Builder
    {
        return $this->media()->where('collection_name', 'default');
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function sights(): HasMany
    {
        return $this->hasMany(Sight::class);
    }

    public function hotels(): HasManyThrough
    {
        return $this->hasManyThrough(Hotel::class, District::class);
    }

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'instance');
    }

    public function scopeShownInBestDeals(Builder $query): Builder
    {
        return $query->where('show_in_best_deals', true);
    }


    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('original')->format('webp')->nonQueued();
    }


}
