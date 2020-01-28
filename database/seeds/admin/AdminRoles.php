<?php

use Illuminate\Database\Seeder;
use App\Facades\RoleServiceFacade as RoleService;
use App\Facades\PermissionServiceFacade as PermissionService;

class AdminRoles extends Seeder
{

    /**
     * Роли пользователей.
     * @var array
     */
    protected $roles = [
        ['name' => 'admin', 'display_name' => 'Администратор', 'description' => ''],
        ['name' => 'agent', 'display_name' => 'Агент', 'description' => '']
    ];

    /**
     * Типы разрешений.
     * @var array
     */
    protected $permissionTypes = [
        ['id' => 1,     'name' => 'Users',              'display_name' => 'Пользователи'],
        ['id' => 100,   'name' => 'Settings',           'display_name' => 'Настройки системы'],
        ['id' => 40,    'name' => 'Temp Sites',         'display_name' => 'Временные сайты'],
        ['id' => 41,    'name' => 'Temp Links',         'display_name' => 'Локальные ссылки'],
        ['id' => 50,    'name' => 'Public Sites',       'display_name' => 'Публичные сайты']
    ];

    /**
     * Разрешения.
     * @var array
     */
    protected $permissions = [

        // Пользователи
        ['type_id' => 1, 'name' => 'users.index',       'display_name' => 'Просмотр всех пользователей',    'roles' => ['admin'], 'main' => true],
        ['type_id' => 1, 'name' => 'users.create',      'display_name' => 'Создание пользователя',          'roles' => ['admin']],
        ['type_id' => 1, 'name' => 'users.show',        'display_name' => 'Просмотр пользователя',          'roles' => ['admin']],

        // Настройки системы
        ['type_id' => 100, 'name' => 'settings.index',          'display_name' => 'Настройки системы',                      'roles' => ['admin'], 'main' => true],
        ['type_id' => 100, 'name' => 'settings.roles.update',   'display_name' => 'Управление ролями и разрешениями', 'roles' => ['admin']],

        // Локальные сайты
        ['type_id' => 40, 'name' => 'temp.sites.index',         'display_name' => 'Получение всех сайтов', 'roles' => ['admin']],
        ['type_id' => 40, 'name' => 'temp.sites.create',        'display_name' => 'Создания сайта', 'roles' => ['admin']],
        ['type_id' => 40, 'name' => 'temp.sites.update',        'display_name' => 'Обновление сайта', 'roles' => ['admin']],
        ['type_id' => 40, 'name' => 'temp.sites.show',          'display_name' => 'Просмотр сайта', 'roles' => ['admin']],
        ['type_id' => 40, 'name' => 'temp.sites.moderation',    'display_name' => 'Модерация локального сайта', 'roles' => ['admin']],
        ['type_id' => 40, 'name' => 'temp.sites.delete',        'display_name' => 'Удаление локального сайта', 'roles' => ['admin']],

        // Локальные ссылки
        ['type_id' => 41, 'name' => 'temp.links.moderation',    'display_name' => 'Модерация локальной ссылки', 'roles' => ['admin']],
        ['type_id' => 41, 'name' => 'temp.links.create',        'display_name' => 'Создание ссылки', 'roles' => ['admin']],

        // Публичные сайты
        ['type_id' => 50, 'name' => 'main.sites.index',       'display_name' => 'Просмотр всех сайтов',   'roles' => ['admin'], 'main' => true],
        ['type_id' => 50, 'name' => 'main.sites.create',      'display_name' => 'Создание сайта',         'roles' => ['admin']],
        ['type_id' => 50, 'name' => 'main.sites.show',        'display_name' => 'Просмотр сайта',         'roles' => ['admin']],
        ['type_id' => 50, 'name' => 'main.sites.update',      'display_name' => 'Обновление сайта',       'roles' => ['admin']],
        ['type_id' => 50, 'name' => 'main.sites.delete',      'display_name' => 'Удаление сайта',         'roles' => ['admin']]

    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Создание ролей.
        $roles = new \Illuminate\Database\Eloquent\Collection();
        foreach($this->roles as $role) {
            $role = RoleService::store($role);
            $roles->add($role);
        }

        // Создание типов разрешений.
        foreach($this->permissionTypes as $permissionType)
            PermissionService::permissionTypeStore($permissionType);

        // Создание разрешений и присвоение ролям.
        foreach($this->permissions as $permission) {
            $pm = PermissionService::store(
                [
                    'type_id'       => $permission['type_id'],
                    'name'          => $permission['name'],
                    'display_name'  => $permission['display_name']
                ]
            );

            foreach($permission['roles'] as $role) {
                $role = $roles->where('name', '=', $role)->first();
                $role->permissions()->attach($pm);
            }
        }
    }
}
