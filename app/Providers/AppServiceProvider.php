<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        \PagSeguro\Library::initialize();
        \PagSeguro\Library::cmsVersion()->setName("MarketPlace2")->setRelease("1.0.0");
        \PagSeguro\Library::moduleVersion()->setName("MarketPlace2")->setRelease("1.0.0");

    }
}
