<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Cookie extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;


    protected $fillable = [
        'title_l',
        'description_l',
        'key',
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public array $translatable = [
        'title_l',
        'description_l'
    ];

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('title_l')
        );
    }

    protected function description(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => $value ?? $this->getAttributeValue('description_l')
        );
    }

}
