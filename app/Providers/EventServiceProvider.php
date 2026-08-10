<?php

namespace App\Providers;

use App\Events\ContractSuspended;
use App\Events\PaymentConfirmed;
use App\Events\TicketCreated;
use App\Listeners\BlockContractLinks;
use App\Listeners\LogPaymentActivity;
use App\Listeners\SendTicketNotification;
use App\Listeners\UnblockContractLinks;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PaymentConfirmed::class => [
            UnblockContractLinks::class,
            LogPaymentActivity::class,
        ],
        ContractSuspended::class => [
            BlockContractLinks::class,
        ],
        TicketCreated::class => [
            SendTicketNotification::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
