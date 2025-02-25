<?php

namespace App\Models;

use App\Enums\ApplicationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'email',
        'phone',
        'body',
    ];

    protected $casts = [
        'type' => ApplicationType::class
    ];
}
