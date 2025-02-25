<?php

namespace App\Providers;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Helpers\CurrencyStorage;
use App\Mixins\StrOnlyNumber;
use Illuminate\Support\Str;
use ReflectionException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyStorage::class, function() {
            return new CurrencyStorage();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws ReflectionException
     */
    public function boot(): void
    {
        # Выкидывает исключение, если происходит N+1
        Model::preventLazyLoading(!app()->isProduction());
        # Выкидывает исключение, если происходит попытка обновить поля модели, которые отсутствуют в fillable
        Model::preventSilentlyDiscardingAttributes(!app()->isProduction());
        # Если запрос в БД выполняется дольше, чем ..., то оповещаем команду
        DB::whenQueryingForLongerThan(500, function (Connection $connection) {
            // todo Notify development team...
        });
        Str::mixin(new StrOnlyNumber);
    }
}
