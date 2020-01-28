<?php

namespace App\Transformers;

use App\Models\Role;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'permissions', 'users'
    ];

    /**
     * A Fractal transformer
     * @param Role $role
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id'            => (int) $role->id,
            'name'          => (string) $role->name,
            'display_name'  => (string) $role->display_name,
            'description'   => (string) $role->description
        ];
    }

    /**
     * Получение разрешений для роли.
     * @param Role $role
     * @return \League\Fractal\Resource\Collection
     */
    public function includePermissions(Role $role)
    {
        return $this->collection($role->permissions, new PermissionTransformer());
    }

    /**
     * Получение пользователей текущей роли.
     * @param Role $role
     * @return \League\Fractal\Resource\Collection
     */
    public function includeUsers(Role $role)
    {
        return $this->collection($role->users, new UserTransformer());
    }

}
