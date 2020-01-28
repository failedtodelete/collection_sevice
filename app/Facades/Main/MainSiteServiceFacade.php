<?php

namespace App\Facades\Main;

use Illuminate\Support\Facades\Facade;

class MainSiteServiceFacade extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MainSiteService';
    }
}
