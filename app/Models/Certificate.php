<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_certificate_id',
        'currency_id',
        'code',
        'paid_at',
        'used_at',
        'used_by_user_id',
        'bought_by_user_id',
        'is_seen',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'used_at' => 'datetime',
        'is_seen' => 'integer',
    ];

    protected $with = [
        'baseCertificate',
        'currency'
    ];

    protected $appends = [
        'amount'
    ];

    protected function isUsed(): Attribute
    {
        return new Attribute(
            get: fn() => !empty($this->used_at)
        );
    }

    protected function isPaid(): Attribute
    {
        return new Attribute(
            get: fn() => !empty($this->paid_at)
        );
    }

    protected function amount(): Attribute
    {
        return new Attribute(
            get: fn() => $this->baseCertificate->amount
        );
    }

    protected function amountTitle(): Attribute
    {
        return new Attribute(
            get: fn() => $this->currency->symbol . ' ' . number_format($this->amount, 0, '', ' ')
        );
    }

    public function baseCertificate(): BelongsTo
    {
        return $this->belongsTo(BaseCertificate::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function boughtByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bought_by_user_id');
    }

    public function usedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }

    public function scopeNotUsed(Builder $query): Builder
    {
        return $query->whereNull('used_at');
    }

    public function scopeUnseen(Builder $query): Builder
    {
        return $query->where('is_seen', false);
    }

    public function markAsSeen(): void
    {
        if(!$this->is_seen){

            $this->is_seen = true;
            $this->save();

        }
    }
}
