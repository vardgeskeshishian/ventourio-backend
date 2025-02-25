<?php

namespace App\Models;

use App\Enums\LanguageType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Language extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    /**
     * @var array
     */
    protected $fillable = [
        'title_l',
        'code',
        'type',
        'flag',
        'is_rtl',
        'is_active',
        'is_default',
        'localization_json'
    ];

    protected $casts = [
        'type' => LanguageType::class,
        'is_rtl' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'localization_json' => 'array',
    ];

    protected $with = [
        'media'
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

    public function scopeActive($query)
    {
        $query->where('is_active', 1);
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

}
