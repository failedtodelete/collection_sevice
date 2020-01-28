<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PermissionServiceFacade extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor() { return 'PermissionService'; }
}