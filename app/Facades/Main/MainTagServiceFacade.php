<?php

namespace App\Facades\Main;

use Illuminate\Support\Facades\Facade;

class MainTagServiceFacade extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MainTagService';
    }
}
