<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTwoFa extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'code',
    ];
}
