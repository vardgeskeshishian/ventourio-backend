<?php

namespace App\Models;

use App\Enums\CreditCardType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'holder_name',
        'type',
        'number',
        'exp_month',
        'exp_year',
        'svc'
    ];

    protected $hidden = [
        'number',
        'exp_month',
        'exp_year',
        'svc',
    ];

    protected $casts = [
        'type' => CreditCardType::class
    ];
}
