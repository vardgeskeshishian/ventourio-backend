<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class ArticleCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'title_l',
        'color_hex',
        'parsing_source',
    ];

    public array $translatable = [
        'title_l'
    ];

    protected $with = [
        'page'
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function page(): MorphOne
    {
        return $this->morphOne(Page::class, 'instance');
    }

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('title_l')
        );
    }
}
