<?php

namespace App\Models;

use App\Services\Web\Continent\Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Continent extends Model
{
    use HasFactory, HasTranslations, HasRelationships;

    protected $fillable = [
        'title_l',
        'parsing_source'
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

    protected function breadcrumbs(): Attribute
    {
        return new Attribute(
            get: fn() => Helper::breadcrumbs($this)
        );
    }

    public function cities(): HasManyDeep
    {
        return $this->hasManyDeep(City::class, [Country::class, Region::class]);
    }

    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'instance');
    }
}
