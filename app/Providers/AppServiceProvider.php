<?php

namespace App\Providers;

use App\Http\Api\NewsCategorySubscriptionApiInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NewsCategorySubscriptionApiInterface::class, function () {
            switch (request()->header('Api-Version', 1)) {
                case 1:
                    return new \App\Http\Api\v1\NewsCategorySubscriptionApi;
                case 2:
                    return new \App\Http\Api\v2\NewsCategorySubscriptionApi;
                default:
                    throw new \ErrorException('Incorrect Api-Version');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
