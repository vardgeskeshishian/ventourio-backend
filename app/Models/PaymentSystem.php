<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PaymentSystem extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'title_l',
        'payment_system',
        'enabled',
    ];

    protected $casts = [
        'title' => 'array',
        'enabled' => 'bool',
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

    public function ways(): HasMany
    {
        return $this->hasMany(PaymentWay::class);
    }
}
