<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class FacilityCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'title_l'
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

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
