<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Permission;
use App\Facades\PermissionServiceFacade as PermissionService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Models\Role;

class RoleService extends BaseService
{

    /**
     * RoleService constructor.
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    /**
     * Создание роли.
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function store($data)
    {

        $rules = [
            'name' => 'required|string',
            'display_name' => 'required|string',
            'description' => 'nullable|string'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        return Role::create($data);

    }

    /**
     * Переключение разрешения для роли.
     * @param $data
     * @param $id
     * @return Role
     * @throws CustomException
     * @throws ValidationException
     */
    public function toggle_permission($data, $id)
    {

        $rules = [
            'permission_id' => 'required|numeric|exists:mysql_admin.permissions,id',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Получение роли.
        $role = $this->findOrFail($id);
        // Получение разрешения.
        $permission = PermissionService::findOrFail($data['permission_id']);

        // Поиск разрешения у роли.
        $exist = $role->permissions()->find($data['permission_id']);
        return $exist ? $this->remove_permission($role, $permission) : $this->add_permission($role, $permission);
    }

    /**
     * Удаление разрешения у роли.
     * @param Role $role
     * @param Permission $permission
     * @return Role
     */
    private function remove_permission(Role $role, Permission $permission)
    {

        // Если это разрешение главное, отключение всех дочерних разрешений.
        if ($permission->main) {

            // Отсоедение разрешения от роли.
            PermissionService::where('type_id', '=', $permission->type_id)->each(function($permission) use ($role) {
                $role->permissions()->detach($permission);
            });

        } else {

            // Отсоедение разрешения от роли.
            $role->permissions()->detach($permission);
        }

        $role->refresh();
        return $role;
    }

    /**
     * Добавление разрешения для роли.
     * @param Role $role
     * @param Permission $permission
     * @return Role
     * @throws CustomException
     */
    public function add_permission(Role $role, Permission $permission)
    {
        // Поиск разрешения у роли.
        if ($role->permissions()->find($permission->id))
            throw new CustomException('Permission already exists for the role');

        // Присоединение разрешения к роли.
        $role->permissions()->attach($permission);

        // Если текущее разрешение не является главным и главное разрешение не включено,
        // присоединение главного разрешения к роли.
        if (!$permission->main) {

            // Получение главного разрешения в группе.
            $main_permission = PermissionService::where('type_id', '=', $permission->type_id)
                ->where('main', '=', true)->first();

            if ($main_permission && !$role->permissions()->find($main_permission->id)) {
                $role->permissions()->attach($main_permission);
            }
        }

        $role->refresh();
        return $role;

    }

}
