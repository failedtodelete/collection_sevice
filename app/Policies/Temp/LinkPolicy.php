<?php

namespace App\Policies\Temp;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LinkPolicy
{
    use HandlesAuthorization;

    /**
     * Возможность модерирования ссылки.
     * @param User $user
     * @return bool
     */
    public function moderation(User $user)
    {
        return $user->access('temp.links.moderation');
    }

    /**
     * Создание сайта.
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->access('temp.links.create');
    }

}
