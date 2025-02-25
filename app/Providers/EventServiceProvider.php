<?php

namespace App\Providers;

use App\Events\BookingCancelRequested;
use App\Events\BookingConfirmed;
use App\Events\BookingCreated;
use App\Events\CertificateCreated;
use App\Events\CertificatePaid;
use App\Events\HotelDiscountWasChanged;
use App\Events\InstanceWithSubscribersCreated;
use App\Events\RegisteredLazy;
use App\Listeners\AddHotelDiscountToRoomBases;
use App\Listeners\NotifySubscribers;
use App\Listeners\SendBookingVoucher;
use App\Listeners\SendCertificate;
use App\Listeners\SendPaymentRequisitesForBooking;
use App\Listeners\SendPaymentRequisitesForCertificate;
use App\Listeners\SendRegisteredLazyNotification;
use App\Models\Application;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\BalanceChange;
use App\Models\Booking;
use App\Models\City;
use App\Models\CompanyService;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\Hotel;
use App\Models\Region;
use App\Models\Subscriber;
use App\Observers\ApplicationObserver;
use App\Observers\ArticleCategoryObserver;
use App\Observers\ArticleObserver;
use App\Observers\BalanceChangeObserver;
use App\Observers\BookingObserver;
use App\Observers\CityObserver;
use App\Observers\CompanyServiceObserver;
use App\Observers\ContinentObserver;
use App\Observers\CountryObserver;
use App\Observers\CurrencyObserver;
use App\Observers\DistrictObserver;
use App\Observers\HotelObserver;
use App\Observers\RegionObserver;
use App\Observers\SubscriberObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        RegisteredLazy::class => [
            SendRegisteredLazyNotification::class
        ],
        BookingCreated::class => [
            SendPaymentRequisitesForBooking::class
            // todo add notification to admins
        ],
        BookingConfirmed::class => [
            SendBookingVoucher::class
        ],
        BookingCancelRequested::class => [
            // todo add notification to admin
        ],
        InstanceWithSubscribersCreated::class => [
            NotifySubscribers::class
        ],
        CertificateCreated::class => [
            SendPaymentRequisitesForCertificate::class
        ],
        CertificatePaid::class => [
            SendCertificate::class
        ],
        HotelDiscountWasChanged::class => [
            AddHotelDiscountToRoomBases::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    protected $observers = [
        BalanceChange::class => [BalanceChangeObserver::class],
        Currency::class => [CurrencyObserver::class],
        Application::class => [ApplicationObserver::class],
        Continent::class => [ContinentObserver::class],
        Country::class => [CountryObserver::class],
        Region::class => [RegionObserver::class],
        City::class => [CityObserver::class],
        District::class => [DistrictObserver::class],
        Hotel::class => [HotelObserver::class],
        CompanyService::class => [CompanyServiceObserver::class],
        Article::class => [ArticleObserver::class],
        ArticleCategory::class => [ArticleCategoryObserver::class],
        Booking::class => [BookingObserver::class],
        Subscriber::class => [SubscriberObserver::class]
    ];
}
