<?php

namespace App\Models;

use App\Enums\BalanceChangeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BalanceChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id',
        'model_type',
        'amount',
        'type',
        'user_id',
        'remark',
    ];

    protected $casts = [
        'type' => BalanceChangeType::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
