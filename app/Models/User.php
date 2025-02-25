<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'gender',
        'country_id',
        'password',
        'password_updated_at',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_updated_at' => 'datetime',
        'gender' => Gender::class
    ];

    protected $appends = ['full_name'];

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? "{$this->first_name} {$this->last_name}"
        );
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? $this->getFirstMedia('avatar')?->getFullUrl()
        );
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(UserSocial::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function hasSocialAccount($provider): bool
    {

        return $this->socialAccounts()
                        ->where('provider', $provider)
                        ->exists();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function availableCertificates(): HasMany
    {
        return $this->boughtCertificates()
            ->whereNull('used_at')
            ->whereNotNull('paid_at');
    }

    public function boughtCertificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'bought_by_user_id');
    }

    public function usedCertificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'used_by_user_id')
            ->orderByDesc('used_at');
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(
            Hotel::class,
            UserFavorite::class,
            'user_id',
            'hotel_id'
        );
    }

    public function twoFa(): HasOne
    {
        return $this->hasOne(UserTwoFa::class);
    }

    private function clearTwoFaCode(): void
    {
        $this->twoFa()->delete();
    }

    public function updateTwoFaCode($code): void
    {
        $this->clearTwoFaCode();

        $this->twoFa()->create(['code' => $code]);
    }

    public function validateTwoFaCode($code): bool
    {
        $code =  $this->twoFa()->where('code', $code)->exists();

        if($code)
        {
            $this->clearTwoFaCode();

            return true;
        }

        return false;
    }


    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('original')->format('webp')->nonQueued();
    }


}
