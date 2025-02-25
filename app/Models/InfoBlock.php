<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class InfoBlock extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'alias',
        'page_id',
        'content_l'
    ];

    protected array $translatable = [
        'content_l'
    ];

    protected function content(): Attribute
    {
        return new Attribute(
            get: fn($value) => json_decode($value, true) ?? $this->getAttributeValue('content_l')
        );
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

}
