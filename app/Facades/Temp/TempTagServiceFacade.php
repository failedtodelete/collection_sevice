<?php

namespace App\Facades\Temp;

use Illuminate\Support\Facades\Facade;

class TempTagServiceFacade extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'TempTagService';
    }
}
