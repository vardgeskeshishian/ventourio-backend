<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class Sight extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'title_l',
        'city_id'
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

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'instance');
    }
}
