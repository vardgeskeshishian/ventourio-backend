<?php

namespace App\Models;

use App\Services\Web\District\Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class District extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'is_common',
        'city_id',
        'title_l',
    ];

    protected $casts = [
        'is_common' => 'boolean'
    ];

    public array $translatable = [
        'title_l',
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

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'instance');
    }
}
