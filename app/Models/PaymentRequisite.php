<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequisite extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('is_active', true);
    }

}
