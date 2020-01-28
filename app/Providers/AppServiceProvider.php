<?php

namespace App\Providers;

use App\Models\Temp\Site;
use App\Observers\Temp\SiteObserver;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use App\Traits\Helper;

class AppServiceProvider extends ServiceProvider
{
    use Helper;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();

        // Инициализация сервисов, внедрнение соответствующей модели для каждого инстанса.
        foreach(config('app.services') as $service => $model) {
            $this->app->singleton($this->classBasename($service), function($app) use ($service, $model) {
                return new $service(new $model);
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // Наблюдатели методов моделей.
        Site::observe(SiteObserver::class);
    }
}
