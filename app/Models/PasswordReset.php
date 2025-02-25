<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordReset extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'token',
        'created_at',
    ];

    protected $primaryKey = 'email';

    public $timestamps = false;

    public $incrementing = false;

    public function isExpire(): bool
    {
        if ($this->created_at > now()->addHour()) {

            $this->delete();

            return true;
        }

        return false;
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

}
