<?php

namespace App\Providers;

use App\Traits\Helper;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{

    use Helper;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

}
