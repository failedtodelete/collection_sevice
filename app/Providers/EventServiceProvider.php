<?php

namespace App\Providers;

use App\Events\ConfirmationLink;
use App\Events\ConfirmationSite;
use App\Listeners\PaymentForLink;
use App\Listeners\PaymentForSite;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ConfirmationLink::class => [
            PaymentForLink::class
        ],
        ConfirmationSite::class => [
            PaymentForSite::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
