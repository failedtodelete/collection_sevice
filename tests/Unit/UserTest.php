<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{

    /**
     * Логин пользователя.
     * Отправка запроса на сервер, получение ответа в виде данных токена.
     */
    public function testLogin()
    {
        // Создание пользователя для дальнейшей аутентификации.
        factory(User::class)->create(['email' => 'test@test.test', 'password' => Hash::make('123123')]);

        // Аутентификация пользователя
        $response = $this->ajax('POST', 'api/auth/login', ['email' => 'test@test.test', 'password' => '123123'], [], []);
        $response->assertStatus(200)->assertJsonStructure([
            'access_token', 'token_type', 'expires_in'
        ]);
    }

    /**
     * Выход пользователя.
     * Аутентификация пользователя и выход.
     *
     */
    public function testLogout()
    {
        // Создание пользователя для дальнейшей аутентификации.
        $user = factory(User::class)->create(['email' => 'test@test.test', 'password' => Hash::make('123123')]);

        // Аутентификация пользователя
        $auth_response = $this->ajax('POST', 'api/auth/login', ['email' => 'test@test.test', 'password' => '123123'], [], []);
        $auth_response->assertStatus(200)->assertJsonStructure([
            'access_token', 'token_type', 'expires_in'
        ]);

        // Выход пользователя из системы.
        $response = $this->ajax('POST', 'api/auth/logout', [], [], ['auth_current' => $user->id]);
        $response->assertStatus(200)->assertJsonStructure(['message']);

    }

    /**
     * Сброс токена.
     * Аутентификация пользоватя, получение токена.
     * Сброс токена. Проверка работы старого токена и нового.
     */
    public function testRefreshToken()
    {
        // Создание пользователя для дальнейшей аутентификации.
        factory(User::class)->create(['email' => 'test@test.test', 'password' => Hash::make('123123')]);
        $user = factory(User::class)->create(['email' => 'test2@test.test', 'password' => Hash::make('123123')]);

        // Аутентификация пользователя
        $login_response = $this->ajax('POST', 'api/auth/login', ['email' => 'test2@test.test', 'password' => '123123'], [], []);
        $login_response->assertStatus(200)->assertJsonStructure([
            'access_token', 'token_type', 'expires_in'
        ]);

        // Сброс токена пользователя
        $refresh_request = $this->ajax('POST', 'api/auth/refresh', [], ['Authorization' => 'Bearer ' . $login_response->decodeResponseJson()['access_token']], []);
        $refresh_request->assertStatus(200)->assertJsonStructure([
            'access_token', 'token_type', 'expires_in'
        ]);

        // Выход пользователя.
        Auth::logout();

        // Сравнение токенов.
        $old_token = $login_response->decodeResponseJson()['access_token'];
        $new_token = $refresh_request->decodeResponseJson()['access_token'];
        $this->assertTrue((bool) $old_token !== $new_token);

        // Проверка работоспособности нового токена.
        $new_request = $this->ajax('GET', 'api/users', [], ['Authorization' => 'Bearer ' . $new_token], []);
        $new_request->assertStatus(403);

        // Выход пользователя.
        Auth::logout();

        // Проверка работоспособности старого токена.
        $old_request = $this->ajax('GET', 'api/users', [], ['Authorization' => 'Bearer ' . $old_token], []);
        $old_request->assertStatus(401);

    }

    /**
     * Создание пользователя.
     * Создание модели пользователя от не аутентифицированного пользователя.
     * Создание модели от пользователя, не имеющего разрешения.
     * Создание модели от пользоватля, имеющего разрешение.
     */
    public function testCreationUser()
    {
        // Модель пользователя
        $user = [
            'name' => 'test123',
            'email' => 'test@test.com',
            'password' => 'test123',
            'role_id' => 1
        ];

        // Отправка запроса от не аутентифицированного пользователя.
        $response = $this->ajax('POST', 'api/users', $user, [], []);
        $response->assertStatus(401);

        // Отправка запроса от пользователя, НЕ ИМЕЮЩЕГО разрешения на выполнение действия.
        $response = $this->ajax('POST', 'api/users', $user, [], ['auth' => '!users.create']);
        $response->assertStatus(403);

        // Отправка запроса от пользователя, ИМЕЮЩЕГО разрешения на выполнение действия.
        $response = $this->ajax('POST', 'api/users', $user, [], ['auth' => 'users.create']);
        $response->assertStatus(200)->assertJsonStructure([
            'id', 'name', 'email', 'role', 'balance', 'paid_out'
        ]);

    }

    /**
     * Получение всех пользователей.
     * Создание нескольких пользователей - проверка на количество пользователей в ответе.
     */
    public function testIndexUsers()
    {
        $users = factory(User::class, rand(5, 20))->create();
        $response = $this->ajax('GET', '/api/users', [], [], ['auth' => 'users.index']);
        $response->assertStatus(200)
            ->assertJsonCount($users->count() + 1, 'data');
    }

    /**
     * Получение пользователя. */
    public function testShowUser()
    {
        $user = factory(User::class)->create();
        $response = $this->ajax('GET', "api/users/{$user->id}", [], [], ['auth' => 'users.show']);
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id
            ]);
    }

    /**
     * Профиль пользователя.
     * Получение профиля текущего аутентифицированного пользователя с ролью и разрешениями.
     */
    public function testProfile()
    {
        $response = $this->ajax('GET', '/api/profile', [], [], ['auth' => 'admin']);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'name', 'email', 'balance', 'paid_out',
                'role' => [
                    'permissions'
                ]
            ]);
    }

}
