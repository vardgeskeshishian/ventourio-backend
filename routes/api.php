<?php

use App\Enums\Helper;
use App\Enums\SocialProvider;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\CitiesInformationParsingController;
use App\Http\Controllers\Admin\CountriesInformationParsingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EditorController;
use App\Http\Controllers\Admin\ParsingController;
use App\Http\Controllers\Web\ArticleCategoryController as WebArticleCategoryController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyServiceController;
use App\Http\Controllers\Admin\ContinentController;
use App\Http\Controllers\Admin\CookieController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CreditCardController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\FacilityCategoryController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\HotelController as AdminHotelController;
use App\Http\Controllers\Admin\InfoBlockController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MeController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentRequisiteController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\QuestionAnswerController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomBaseController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\SightController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\UserTwoFaController;
use App\Http\Controllers\Web\ApplicationController;
use App\Http\Controllers\Web\ArticleController as WebArticleController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BookingController as BookingWebController;
use App\Http\Controllers\Web\CityController as CityWebController;
use App\Http\Controllers\Web\CompanyServiceController as CompanyServiceWebController;
use App\Http\Controllers\Web\ContactUsController as WebContactUsController;
use App\Http\Controllers\Admin\ContactUsController;
use App\Http\Controllers\Web\CookieController as CookieWebController;
use App\Http\Controllers\Web\CountryController as CountryWebController;
use App\Http\Controllers\Web\CurrencyController as CurrencyWebController;
use App\Http\Controllers\Web\ForgotPasswordController;
use App\Http\Controllers\Web\GoGlobalController;
use App\Http\Controllers\Web\GuideBookController;
use App\Http\Controllers\Web\HotelController;
use App\Http\Controllers\Web\LanguageController as LanguageWebController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\PageController as PageWebController;
use App\Http\Controllers\Web\PopularDestinationsController;
use App\Http\Controllers\Web\ReviewController as ReviewWebController;
use App\Http\Controllers\Web\SocialController;
use App\Http\Controllers\Web\SubscriberController;
use App\Http\Controllers\Web\UserController as UserWebController;
use App\Http\Controllers\Web\UserFavoriteController;
use App\Http\Controllers\Web\CertificateController as GiftCertificateWebController;
use App\Services\LocalizationService;
use App\Services\Parsing\GuideBookParsingService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix(LocalizationService::setLocale())
    ->middleware('setCurrency')
    ->group(function () {

    Route::group(['prefix' => 'web'], function () {

        Route::group(['prefix' => 'guide'], function () {

            Route::get('s/{page:slug}', [GuideBookController::class, 'getPageBySlug']);
            Route::get('book', [GuideBookController::class, 'guideBook']);

            Route::prefix('countries')->group(function () {
                Route::get('', [GuideBookController::class, 'indexCountries']);
                Route::get('{page:slug}', [GuideBookController::class, 'indexCountriesByContinent']);
            });

            Route::prefix('cities')->group(function () {
                Route::get('', [GuideBookController::class, 'indexCities']);
                Route::get('{page:slug}', [GuideBookController::class, 'indexCitiesByContinent']);
            });

            Route::prefix('destination')->group(function () {
                Route::get('{page:slug}', [GuideBookController::class, 'destinationShow']);
                Route::get('{page:slug}/filter/hotels', [GuideBookController::class, 'filterDestinationHotelsByStar']);
            });
        });

        Route::prefix('popular')->group(function () {
            Route::get('destinations', [PopularDestinationsController::class, 'index']);
        });

        Route::group(['prefix' => 'articles'], function () {
            Route::get('', [WebArticleController::class, 'index']);
            Route::get('{page:slug}', [WebArticleController::class, 'show']);
        });

        Route::group(['prefix' => 'article_categories'], function () {
            Route::get('{article_category}', [WebArticleCategoryController::class, 'show']);
            Route::get('{page:slug}/articles', [WebArticleCategoryController::class, 'articles']);
        });

        Route::group(['prefix' => 'auth'], function () {
            Route::post('register', [AuthController::class, 'register']);
            Route::post('login', [AuthController::class, 'login']);
            Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

            Route::group(['prefix' => 'check'], function () {
                Route::post('', [AuthController::class, 'checkEmail']);
            });

            Route::group(['prefix' => 'me', 'middleware' => 'auth:sanctum'], function () {
                Route::get('', [UserWebController::class, 'me']);
                Route::post('', [UserWebController::class, 'update']);
            });

            Route::group(['prefix' => 'social'], function () {

                Route::get('login/{provider}',[SocialController::class, 'socialLogin'])
                    ->where('provider', Helper::implode(SocialProvider::cases(), '|'));
                Route::get('connect/{provider}',[SocialController::class, 'connectSocialLogin'])
                    ->where('provider', Helper::implode(SocialProvider::cases(), '|'))->middleware('auth:sanctum');
                Route::get('login/{provider}/callback',[SocialController::class, 'handleProviderCallback'])
                    ->where('provider', Helper::implode(SocialProvider::cases(), '|'));
                Route::delete('delete/{provider}',[SocialController::class, 'delete'])
                    ->where('provider', Helper::implode(SocialProvider::cases(), '|'))->middleware('auth:sanctum');
            });

            Route::group(['prefix' => '2fa'], function () {
                Route::post('register',[UserTwoFaController::class, 'register']);
                Route::post('login',   [UserTwoFaController::class, 'login']);
            });

            Route::group(['prefix' => 'password'], function () {
                Route::post('email',  [ForgotPasswordController::class, 'sendToEmail']);
                Route::post('reset', [ForgotPasswordController::class, 'resetPassword']);
            });
        });

        Route::group(['prefix' => 'application', 'middleware' => 'captcha'], function () {
            Route::post('', [ApplicationController::class, 'store']);
        });

        Route::group(['prefix' => 'subscribe', 'middleware' => 'captcha'], function () {
            Route::post('', [SubscriberController::class, 'store']);
            Route::get('verify/{token}', [SubscriberController::class, 'verify'])->name('subscriber.verify');
        });

        Route::group(['prefix' => 'hotel'], function () {
            Route::get('search', [HotelController::class, 'search']);
            Route::get('best-deals', [HotelController::class, 'bestDeals']);
            Route::get('favorites', [HotelController::class, 'favorites'])->middleware('auth:sanctum');
            Route::get('filters', [HotelController::class, 'getFilters']);
            Route::get('sorts', [HotelController::class, 'getSorts']);

            Route::group(['prefix' => 'booking'], function () {
                Route::get('', [BookingWebController::class, 'index'])->middleware('auth:sanctum');
                Route::post('', [BookingWebController::class, 'book']);
                Route::get('checkout/offer', [BookingWebController::class, 'checkoutOffer']);
                Route::get('checkout/rooms', [BookingWebController::class, 'checkoutRooms']);
                Route::post('{booking}/cancel', [BookingWebController::class, 'cancel'])->middleware('auth:sanctum');
            });

            Route::get('{hotel:slug}', [HotelController::class, 'get']);
            Route::get('{hotel}/reviews', [HotelController::class, 'reviews']);
            Route::post('{hotel}/reviews', [ReviewWebController::class, 'storeByHotel'])->middleware('auth:sanctum');
        });

        Route::group(['prefix' => 'location'], function () {
            Route::get('search', [LocationController::class, 'search']);
        });

        Route::group(['prefix' => 'page'], function () {
            Route::get('', [PageWebController::class, 'get']);
        });

        Route::get('city/search', [CityWebController::class, 'search']);
        Route::get('language', [LanguageWebController::class, 'index']);
        Route::get('cookie', [CookieWebController::class, 'index']);
        Route::get('currency', [CurrencyWebController::class, 'getAll']);
        Route::get('country/define', [CountryWebController::class, 'define']);
        Route::get('country/nationality', [CountryWebController::class, 'indexNationalities']);

        Route::resource('company_service', CompanyServiceWebController::class, ['only' => [
            'index', 'show'
        ]]);

        Route::resource('contact_us', WebContactUsController::class, ['only' => [
            'store'
        ]]);


        Route::prefix('certificate')->group(function () {
            Route::get('', [GiftCertificateWebController::class, 'index'])->middleware('auth:sanctum');
            Route::post('', [GiftCertificateWebController::class, 'store']);
            Route::get('available-for-purchase', [GiftCertificateWebController::class, 'indexAvailableForPurchase']);
            Route::post('{certificate:code}/use', [GiftCertificateWebController::class, 'use'])->middleware('auth:sanctum');
        });

        Route::group(['prefix' => 'review'], function () {

            Route::middleware('auth:sanctum')
                ->post('', [ReviewWebController::class, 'store']);

            Route::get('rating-categories', [ReviewWebController::class, 'getRatingCategories']);
        });

        Route::middleware('auth:sanctum')
            ->resource('favorite', UserFavoriteController::class);
    });

    Route::group(['prefix' => 'admin'], function () {

        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('', [DashboardController::class, 'index']);
        });

        Route::group(['prefix' => 'parse'], function () {

            Route::get('atOnce', [ParsingController::class, 'atOnce']);
            Route::get('continents', [ParsingController::class, 'continents']);
            Route::get('countries', [ParsingController::class, 'countries']);
            Route::get('cities', [ParsingController::class, 'cities']);
            Route::get('regions', [ParsingController::class, 'regions']);

            Route::post('import', [ParsingController::class, 'import']);

            Route::group(['prefix' => 'guide'], function () {
                Route::get('slider', [GuideBookParsingService::class, 'slider']);
                Route::get('latest_articles', [GuideBookParsingService::class, 'latestArticles']);
            });

            Route::group(['prefix' => 'information'], function () {
                Route::get('countries', [CountriesInformationParsingController::class, 'parseCountriesInformation']);
                Route::get('cities', [CitiesInformationParsingController::class, 'parseCitiesInformation']);
            });
        });

        Route::group(['prefix' => 'auth'], function () {
            Route::post('login', [AdminAuthController::class, 'login']);
        });

        Route::group(['prefix' => 'password'], function () {
            Route::post('email',  [AdminAuthController::class, 'sendToEmail']);
            Route::post('code/check', [AdminAuthController::class, 'passwordCodeCheck']);
        });

        # Роуты требующие админской авторизации
        Route::group(['middleware' => ['auth:sanctum', 'auth.admin']], function () {

            Route::group(['prefix' => 'auth'], function () {

                Route::resource('me', MeController::class);

                Route::group(['prefix' => 'me'], function () {

                    Route::post('logout', [AdminAuthController::class, 'logout']);
                    Route::post('update/password', [AdminAuthController::class, 'passwordUpdate']);

                });
            });

            // language
            Route::post('language-update/{id}', [LanguageController::class, 'update']);
            Route::get('language-json', [LanguageController::class, 'getJson']);

            // currency
            Route::get('currency/{id}/restore', [CurrencyController::class, 'restore']);
            Route::get('currency/{id}/set-main', [CurrencyController::class, 'setIsMain']);

            // cookie
            Route::get('cookie/{id}/restore', [CookieController::class, 'restore']);

            Route::resource('application', AdminApplicationController::class, ['only' => [
                'index', 'show'
            ]]);

            Route::resources([
                'cookie'            => CookieController::class,
                'language'          => LanguageController::class,
                'currency'          => CurrencyController::class,
                'page'              => PageController::class,
                'role'              => RoleController::class,
                'permission'        => PermissionController::class,
                'admin'             => AdminController::class,
                'certificate'       => CertificateController::class,
                'continent'         => ContinentController::class,
                'country'           => CountryController::class,
                'region'            => RegionController::class,
                'city'              => CityController::class,
                'district'          => DistrictController::class,
                'sight'             => SightController::class,
                'hotel'             => AdminHotelController::class,
                'room_type'         => RoomTypeController::class,
                'room_base'         => RoomBaseController::class,
                'room'              => RoomController::class,
                'company_service'   => CompanyServiceController::class,
                'qa'                => QuestionAnswerController::class,
                'review'            => ReviewController::class,
                'article_category'  => ArticleCategoryController::class,
                'article'           => ArticleController::class,
                'tag'               => TagController::class,
                'user'              => UserController::class,
                'facility'          => FacilityController::class,
                'facility_category' => FacilityCategoryController::class,
                'credit_card'       => CreditCardController::class,
                'booking'           => BookingController::class,
                'discount'          => DiscountController::class,
                'payment_requisite' => PaymentRequisiteController::class,
            ]);

            Route::resource('contact_us', ContactUsController::class, ['only' => [
                'index', 'show'
            ]]);

            Route::group(['prefix' => 'booking/external'], function () {
                Route::get('create', [BookingController::class, 'createExternal']);
                Route::post('', [BookingController::class, 'storeExternal']);
                Route::post('cancel', [BookingController::class, 'cancelExternal']);
            });

            Route::post('editor/upload', [EditorController::class, 'upload']);

            Route::get('info-block/format', [InfoBlockController::class, 'getFormat']);

            Route::delete('media/{media}/delete', MediaController::class);

            Route::group(['prefix' => 'goglobal'], function () {

                Route::post('import/hotels', [GoGlobalController::class, 'importHotels']);
                Route::post('import/cities', [GoGlobalController::class, 'importCountriesAndCities']);
                Route::post('update/hotels', [GoGlobalController::class, 'updateHotels']);
                Route::post('attach/cities', [GoGlobalController::class, 'attachCountriesAndCities']);
            });
        });
    });
});
