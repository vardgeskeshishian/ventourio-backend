<?php

namespace App\Models;

use App\Enums\PageType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'instance_id',
        'instance_type',
        'slug',
        'type',
        'content_l',
        'heading_title_l',
        'meta_title_l',
        'meta_description_l',
        'view_count'
    ];

    protected $hidden = [
        'instance_id',
        'instance_type'
    ];

    protected $casts = [
        'type' => PageType::class
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public array $translatable = [
        'content_l',
        'heading_title_l',
        'meta_title_l',
        'meta_description_l'
    ];

    protected function content(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('content_l')
        );
    }

    protected function headingTitle(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('heading_title_l')
        );
    }

    protected function metaTitle(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('meta_title_l')
        );
    }

    protected function metaDescription(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('meta_description_l')
        );
    }

    public function infoBlocks(): HasMany
    {
        return $this->hasMany(InfoBlock::class);
    }

    public function qa(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    public function instance(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeRegion(Builder $query): Builder
    {
        return $query->where('instance_type', Region::class);
    }

    public function scopeHotel(Builder $query): Builder
    {
        return $query->where('instance_type', Hotel::class);
    }

    public function scopeCity(Builder $query): Builder
    {
        return $query->where('instance_type', City::class);
    }

    public function scopeDistrict(Builder $query): Builder
    {
        return $query->where('instance_type', District::class);
    }
}
