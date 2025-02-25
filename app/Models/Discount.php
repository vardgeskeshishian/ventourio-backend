<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'expired_at'
    ];

    protected $casts = [
        'type' => DiscountType::class
    ];

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function roomBases(): HasMany
    {
        return $this->hasMany(RoomBase::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->where('expired_at', '>=', now()->startOfDay())
                ->orWhereNull('expired_at');
        });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expired_at', '<', now()->endOfDay());
    }

    public function apply(float $value): float
    {
        return match ($this->type) {
            DiscountType::PERCENT => $value - ($value / 100 * $this->amount),
            DiscountType::SUBTRACT => $value - $this->amount,
        };
    }

    public function applyRawExpression(string $fieldKey): Expression
    {
        return match ($this->type) {
            DiscountType::PERCENT => DB::raw("$fieldKey - ($fieldKey / 100 * {$this->amount})"),
            DiscountType::SUBTRACT => DB::raw("$fieldKey - {$this->amount}"),
        };
    }

    public function updateRelationPrices(): void
    {
        $this->roomBases()->update(['price' => $this->applyRawExpression('base_price')]);
    }

    public function delete(): ?bool
    {
        $this->hotels()->update(['discount_id' => null]);
        $this->roomBases()->update(['discount_id' => null, 'price' => DB::raw('base_price')]);

        return parent::delete();
    }
}
