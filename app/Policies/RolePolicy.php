<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Обновление данных роли.
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $user->access('settings.roles.update');
    }

}
