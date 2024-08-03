<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Strategies\CheckoutStrategy;
use App\Strategies\TaxAndServiceStrategy;
use App\Strategies\ServiceOnlyStrategy;

class StrategyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CheckoutStrategy::class, function ($app) {
            $strategy = request()->input('strategy', 'tax_and_service');

            if ($strategy === 'service_only') {
                return new ServiceOnlyStrategy();
            }

            return new TaxAndServiceStrategy();
        });
    }

    public function boot()
    {
        //
    }
}

