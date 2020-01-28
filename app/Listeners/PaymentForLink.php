<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Facades\UserServiceFacade as UserService;

class PaymentForLink
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
        UserService::service_payment($event->link->creator_id, config('service-pricing.link_creation_price'), "Начисление средств за создание ссылки «{$event->link->url}»");
    }
}
