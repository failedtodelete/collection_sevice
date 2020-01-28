<?php

use Illuminate\Database\Seeder;
use App\Facades\UserServiceFacade as UserService;

class AdminUsers extends Seeder
{

    protected $users = [
        [
            'name'      => 'mrmusic',
            'email'     => 'mrmusic2301@gmail.com',
            'password'  => '123123',
            'role_id'   => 1
        ],
        [
            'name'      => 'oleg',
            'email'     => 'oleg@arky.site',
            'password'  => '123123',
            'role_id'   => 1
        ],
        [
            'name'      => 'agent1',
            'email'     => 'agent1@mail.ru',
            'password'  => '123123',
            'role_id'   => 2
        ]
    ];

    /**
     * Run the database seeds
     */
    public function run()
    {

        // Создание пользователей.
        factory(\App\Models\User::class, 10)->create();
        foreach($this->users as $user) {
            UserService::store([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'role_id' => $user['role_id'],
             ]);
        }
    }
}
