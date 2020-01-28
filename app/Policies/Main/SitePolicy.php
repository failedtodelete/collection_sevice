<?php

namespace App\Policies\Main;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SitePolicy
{
    use HandlesAuthorization;

    /**
     * Просмотр всех сайтов.
     * @param User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->access('main.sites.index');
    }

    /**
     * Просмотр определенного сайта.
     * @param User $user
     * @return bool
     */
    public function show(User $user)
    {
        return $user->access('main.sites.show');
    }

    /**
     * Создание сайта.
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->access('main.sites.create');
    }

    /**
     * Обновление сайта.
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return  $user->access('main.sites.update');
    }

    /**
     * Удаление сайта.
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->access('main.sites.delete');
    }

}
