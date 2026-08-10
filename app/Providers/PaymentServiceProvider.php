<?php

namespace App\Providers;

use App\Services\PaymentGatewayManager;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class);
    }

    public function provides(): array
    {
        return [PaymentGatewayManager::class];
    }
}
