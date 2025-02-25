<?php

namespace App\Models;

use App\Services\Web\Country\Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Country extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, HasRelationships;

    protected $fillable = [
        'continent_id',
        'title_l',
        'nationality_l',
        'iso_code',
        'geo',
        'external_code',
        'geography_l',
        'description_l',
        'article_l',
        'parsing_source'
    ];

    public array $translatable = [
        'title_l',
        'nationality_l',
        'geography_l',
        'description_l',
        'article_l',
    ];

    protected $with = [
        'media'
    ];

    protected $casts = [
        'geo' => 'array',
    ];

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('title_l')
        );
    }

    protected function nationality(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('nationality_l')
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

    public function continent(): BelongsTo
    {
        return $this->belongsTo(Continent::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'instance');
    }

    protected function flag(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->media()->where('collection_name', 'flag')->first()
        );
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class);
    }

    public function hotels(): HasManyDeep
    {
        return $this->hasManyDeep(Hotel::class, [Region::class, City::class, District::class]);
    }


    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('original')->format('webp')->nonQueued();
    }

}
