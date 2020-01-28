<?php

namespace App\Facades\Main;

use Illuminate\Support\Facades\Facade;

class MainLanguageServiceFacade extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MainLanguageService';
    }
}
