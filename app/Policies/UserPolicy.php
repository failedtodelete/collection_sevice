<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Просмотр всех пользователей.
     * @param User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->access('users.index');
    }

    /**
     * Просмотр определенного пользователя.
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function show(User $user, User $model)
    {
        return (bool) $user->access('users.show') ||
            $user->id == $model->id;
    }

    /**
     * Создание пользователя.
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->access('users.create');
    }

    /**
     * Обновление пользователя.
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function update(User $user, User $model)
    {
        return (bool) $user->access('users.update') ||
            $user->id == $model->id;
    }

    /**
     * Удаление пользователя.
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->access('users.delete');
    }

}
