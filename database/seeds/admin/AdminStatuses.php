<?php

use Illuminate\Database\Seeder;

class AdminStatuses extends Seeder
{

    /**
     * Статусы пользователей.
     * @var array
     */
    protected $statuses = [
        ['name' => 'active', 'display_name' => 'Активен'],
        ['name' => 'inactive', 'display_name' => 'Не активен'],
        ['name' => 'blocked', 'display_name' => 'Заблокирован']
    ];

    /**
     * Создание статусов.
     */
    public function run()
    {
        foreach($this->statuses as $status) {
            \App\Models\UserStatus::create($status);
        }
    }
}
