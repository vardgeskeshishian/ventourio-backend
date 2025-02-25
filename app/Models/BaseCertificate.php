<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaseCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'amount',
        'color_hex'
    ];

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
