<?php

namespace Tests\Unit\Temp;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use App\Models\Temp\Link;
use App\Models\Temp\Type;
use App\Models\User;

class LinkTest extends TestCase
{

    /**
     * Создание ссылки и проверка на её существование в системе.
     * Все пользователи могут создавать ссылки.
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function testCreationLink()
    {
        factory(Type::class)->create();
        factory(User::class)->create();     // Создание пользователя (администратор) для назначения модераторы ссылки.
        $link = factory(Link::class)->create();

        // Создание существующей ссылки.
        $response = $this->ajax('POST', 'api/temp/links?include=creator,moderator', ['url' => $link->url, 'type_id' => 1], [], ['auth' => '!temp.links.create']);
        $response->assertStatus(403);

        // Отправка запроса
        // Проверка установленного статуса и структуры ответа.
        $response = $this->ajax('POST', 'api/temp/links?include=creator,moderator', ['url' => 'http://test.com', 'type_id' => 1], [], ['auth' => 'temp.links.create']);
        $response->assertStatus(200)
            ->assertJson([
                'status' => Link::MODERATION
            ])
            ->assertJsonStructure([
                'id',
                'url',
                'status',
                'creator' => ['id'],
                'moderator' => ['id']
            ]);
    }

    /**
     * Получение доступных ссылок для использования.
     * Данные ссылки получают все пользователи, вне зависимости от роли.
     */
    public function testAvailableLinks()
    {

        factory(Type::class)->create();

        $statuses = [
            Link::MODERATION    => false,
            Link::CONFIRMED     => true,
            Link::CANCELED      => false,
            Link::PUBLISHED     => false,
            Link::HOLDED        => false
        ];

        for($i = 0; $i < 100; $i++) {
            $rand_index = array_rand($statuses, 1);
            foreach($statuses as $key => $status) {
                if ($rand_index == $key) {
                    factory(Link::class)->create([
                        'status' => $key
                    ]);
                }
            }
        }

        // Получение количества доступных ссылок.
        $confirmed_links_count = DB::connection('mysql_temp')->table('links')->where('status', '=', Link::CONFIRMED)->count();

        // Отправка запроса и проверка количества отдаваемых ссылок.
        $response = $this->ajax('GET', "api/temp/links/available", [], [], ['auth' => 'agent']);
        $response->assertStatus(200)->assertJsonCount($confirmed_links_count, 'data');

    }

    /**
     * Модерация созданной ссылки (одобрение)
     * Только пользователь с разрешением возможности модерации может это делать.
     */
    public function testModerationCompleted()
    {

        // Создание ссылки.
        // STATUS [MODERATION] -> CREATOR_ID [1] -> MODERATOR_ID [1]
        factory(Type::class)->create();
        $link = factory(Link::class)->create();

        // Попытка модерации ссылки от имени пользователя не имеющего на это разрешения.
        $response = $this->ajax('POST', "api/temp/links/{$link->id}/confirm?include=creator.balance_transactions", ['value' => true], [], ['auth' => '!temp.links.moderation']);
        $response->assertStatus(403);

        // Модерация ссылки от имени пользователя имеющего разрешение.
        $response = $this->ajax('POST', "api/temp/links/{$link->id}/confirm?include=creator.balance_transactions", ['value' => true], [], ['auth' => 'temp.links.moderation']);

        // Проверка статуса измененной ссылки, создателя,
        // его баланса и существования записи в истории баланса.
        $response
            ->assertStatus(200)
            ->assertJson([
                'id'        => $link->id,
                'status'    => Link::CONFIRMED,
                'creator'   => [
                    'id' => $link->creator->id,
                    'balance' => config('service-pricing.link_creation_price'),
                    'balance_transactions' => [
                        'data' => [
                            0 => [
                                'id' => 1,
                                'amount' => config('service-pricing.link_creation_price')
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /**
     * Модерация созданной ссылки (отклонение)
     * Только пользователь с разрешением возможности модерации может это делать.
     */
    public function testModerationCanceled()
    {
        // Ссоздание ссылки.
        // STATUS [MODERATION] -> CREATOR_ID [1] -> MODERATOR_ID [1]
        factory(Type::class)->create();
        $link = factory(Link::class)->create();

        // Попытка модерации ссылки от имени пользователя не имеющего на это разрешения.
        $response = $this->ajax('POST', "api/temp/links/{$link->id}/confirm?include=creator.balance_transactions", ['value' => true], [], ['auth' => '!temp.links.moderation']);
        $response->assertStatus(403);

        // Модерация ссылки от имени пользователя имеющего разрешение.
        $response = $this->ajax('POST', "api/temp/links/{$link->id}/confirm?include=creator", ['value' => false], [], ['auth' => 'temp.links.moderation']);

        // Проверка статуса изменной ссылки
        // и отсутствия зачислений на баланс пользователя.
        $response
            ->assertStatus(200)
            ->assertJson([
                'id'        => $link->id,
                'status'    => Link::CANCELED,
                'creator'   => [
                    'id' => $link->creator->id,
                    'balance' => 0
                ]
            ]);
    }

}
