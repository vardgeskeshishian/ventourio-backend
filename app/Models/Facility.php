<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Facility extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    public $timestamps = false;

    protected $fillable = [
        'title_l',
        'category_id'
    ];

    public array $translatable = [
        'title_l'
    ];

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('title_l')
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('original')->format('webp')->nonQueued();
    }

}
