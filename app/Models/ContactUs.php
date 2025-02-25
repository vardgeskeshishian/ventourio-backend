<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_service_id',
        'email',
        'body',
    ];

    protected $with = [
        'company_service'
    ];

    public function company_service(): BelongsTo
    {
        return $this->belongsTo(CompanyService::class);
    }
}
