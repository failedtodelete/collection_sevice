<?php

namespace App\Policies\Temp;

use App\Models\Temp\Site;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SitePolicy
{
    use HandlesAuthorization;


    /**
     * Возможность просмотра сайтов.
     * @param User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->access('temp.sites.index');
    }

    /**
     * Создание сайта.
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->access('temp.sites.create');
    }

    /**
     * Обновление сайта.
     * @param User $user
     * @param Site $site
     * @return bool
     */
    public function update(User $user, Site $site)
    {
        return  $user->access('temp.sites.update') ||
            $site->creator->id == $user->id;
    }

    /**
     * Просмотр определенного сайта.
     * @param User $user
     * @param Site $site
     * @return bool
     */
    public function show(User $user, Site $site)
    {
        return $user->access('temp.sites.show') ||
            $site->creator->id == $user->id;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function moderation(User $user)
    {
        return $user->access('temp.sites.moderation');
    }

}
