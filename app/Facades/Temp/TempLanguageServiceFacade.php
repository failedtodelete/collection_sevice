<?php

namespace App\Facades\Temp;

use Illuminate\Support\Facades\Facade;

class TempLanguageServiceFacade extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'TempLanguageService';
    }
}
