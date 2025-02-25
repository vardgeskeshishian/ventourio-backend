<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'verify_token',
        'email_verified_at',
    ];

    public function scopeUnverified(Builder $query): Builder
    {
        return $query->whereNull('email_verified_at');
    }
}
