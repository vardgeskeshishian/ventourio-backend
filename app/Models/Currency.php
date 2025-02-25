<?php

namespace App\Models;

use App\Enums\CacheKey;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class Currency extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_l',
        'code',
        'symbol',
        'is_main',
        'currency_rate',
    ];

    protected $casts = [
        'is_main' => 'bool'
    ];

    /**
     * The attributes for translation
     * @var array
     */
    public array $translatable = [
        'name_l',
    ];

    protected function name(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ?? $this->getAttributeValue('name_l')
        );
    }

    public function scopeMain(Builder $query): Builder
    {
        return $query->where('is_main', true);
    }

    /**
     * @throws Exception
     */
    public static function getMain(): self
    {
        $currency = self::getCached()->where('is_main')->first();

        if ( ! $currency) {
            throw new Exception('No main currency!');
        }

        return $currency;
    }

    /**
     * @throws Exception
     */
    public static function getCached(): Collection
    {
        $currencies = Cache::remember(CacheKey::CURRENCIES->value, now()->addDay(), function () {
            return Currency::all();
        });

        if (empty($currencies) || $currencies->isEmpty()) {
            throw new Exception('No currencies!');
        }

        return $currencies;
    }
}
