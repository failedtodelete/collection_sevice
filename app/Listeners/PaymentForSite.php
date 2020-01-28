<?php

namespace App\Listeners;

use App\Facades\UserServiceFacade as UserService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentForSite
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // Проведение оплаты пользователю за создание ссылки.
        UserService::service_payment($event->site->creator_id, config('service-pricing.site_creation_price'), "Начисление средств за создание cайта «{$event->site->link->url}»");
    }
}
