<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserServiceFacade extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor() { return 'UserService'; }
}