<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name'
    ];

    protected static function booted()
    {
        static::saving(function ($role) {

            $role->guard_name = 'admin';

        });
    }
}
