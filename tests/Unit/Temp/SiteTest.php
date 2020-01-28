<?php

namespace Tests\Unit\Temp;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Temp\Language;
use App\Models\Temp\Link;
use App\Models\Temp\Site;
use App\Models\Temp\Tag;
use App\Models\Temp\Type;
use App\Models\User;
use Tests\TestCase;

class SiteTest extends TestCase
{

    /**
     * Создание сайта на основе ссылки.
     * Когда сайт будет успешно создан, ссылка должна перейти в статус [HOLDED 4]
     * Сам сам должен иметь статус [CREATED 1]
     */
    public function testCreationSite()
    {

        $statuses = [
            Link::MODERATION    => false,
            Link::CONFIRMED     => true,
            Link::CANCELED      => false,
            Link::PUBLISHED     => false,
            Link::HOLDED        => false
        ];

        // Создание общего типа для ссылок.
        factory(Type::class)->create();

        // Перебор доступных статусов, отправка запроса и проверка ответа возможности создания сайта.
        foreach($statuses as $status => $access) {

            // Создание типа и существующей ссылки.
            $link = factory(Link::class)->create(['status' => $status]);

            // Отправка запроса
            $response = $this->ajax('POST', 'api/temp/sites', ['link_id' => $link->id], [], ['auth' => 'temp.sites.create']);
            $response->assertStatus(200);
            if ($access) {
                $response->assertJsonStructure(['id', 'rating', 'thumbnail', 'hash', 'languages', 'tags', 'images', 'creator'])
                    ->assertJson([
                        'status' => Site::CREATED,
                        'link' => [
                            'id' => $link->id,
                            'status' => Link::HOLDED
                        ]
                    ]);
            } else $response->assertJson(['status' => false]);
        }
    }

    /**
     * Обновление сайта.
     * Создание модели сайта через factory и изменение данных через запрос.
     */
    public function testUpdateSite()
    {
        Storage::fake('public');
        $current_user = factory(User::class)->create();
        factory(Type::class)->create();
        factory(Link::class)->create();
        factory(Language::class, 2)->create();
        factory(Tag::class, 10)->create();
        $site = factory(Site::class)->create(['status' => Site::CREATED]);

        // Отправка запроса для обновление данных сайта.
        $response = $this->ajax('PUT', "api/temp/sites/{$site->id}", [
            'languages' => [1, 2],
            'tags' => [3, 4, 6, 7],
            'images' => [UploadedFile::fake()->image('main1.jpg')]
        ], [], ['auth_current' => $current_user->id]);

        // Проверка существования изображений у сайта.
        $response->assertJsonCount(1, 'images.data');

        // Проверка существования изображений на сервере.
        $this->assertTrue((bool) count(Storage::disk('public')->files($site->hash)));

        // Проверка соответствния данных.
        $response->assertJson([
            'languages' => ['data' => [
                    0 => ['id' => 1],
                    1 => ['id' => 2]
                ]
            ],
            'tags' => ['data' => [
                    0 => ['id' => 3],
                    1 => ['id' => 4],
                    2 => ['id' => 6],
                    3 => ['id' => 7]
                ]
            ]
        ]);
    }

    /**
     * Получение сайта владельцем.
     * Пользователь, который не является владельцем текущего сайта не должен получать к нему доступ.
     */
    public function testShowSite()
    {
        $current_user = factory(User::class)->create();     // Создание пользователя. Основателя ссылки.
        factory(Type::class)->create();     // Создание типа для ссылки.
        factory(Link::class)->create();     // Создание ссылки пользователя.
        $site = factory(Site::class)->create();     // Создание сайта текущей ссылки.

        // Запрос получения сайта от иного зарегистрированного пользователя (не владющего сайтом).
        $response = $this->ajax('GET', "/api/temp/sites/{$site->id}", [], [], ['auth' => 'agent']);
        $response->assertJson(['status' => false]);

        // Запрос получение сайта от создателя (владельца).
        $response = $this->ajax('GET', "/api/temp/sites/{$site->id}", [], [], ['auth_current' => $current_user->id]);
        $response->assertJson([
            'id' => $site->id,
            'status' => Site::CREATED,
            'link' => [
                'status' => Link::HOLDED
            ]
        ]);

        // Запрос получения сайта от пользователя, имеющего разрешение.
        $response = $this->ajax('GET', "/api/temp/sites/{$site->id}", [], [], ['auth' => 'temp.sites.show']);
        $response->assertJson([
            'id' => $site->id,
            'status' => Site::CREATED,
            'link' => [
                'status' => Link::HOLDED
            ]
        ]);

        // Запрос получения сайта от пользователя, НЕ имеющего разрешение.
        $response = $this->ajax('GET', "/api/temp/sites/{$site->id}", [], [], ['auth' => '!temp.sites.show']);
        $response->assertStatus(403);

    }

    /**
     * Получение сайтов текущего пользователя.
     * Проверка возможности получения сайтов передавая идентификатор статуса.
     */
    public function testIndexSites()
    {

        $statuses = [Site::CREATED, Site::MODERATION];

        factory(Type::class)->create();
        $users = factory(User::class, 2)->create();
        $links = factory(Link::class, 20)->create();
        foreach($links as $link) {
            factory(Site::class)->create([
                'link_id' => $link->id,
                'creator_id' => $users->random(1)->first()->id,
                'status' => rand(0, 1) ? Site::CREATED : Site::MODERATION
            ]);
        }

        // Перебор возможных статусов сайтов.
        // Получение ожидаемого количества сайтов у пользователя.
        // Отправка запроса и сравнивание количества.
        foreach($statuses as $status) {
            // Получение ожидаемого количества сайтов.
            $site_count = DB::connection('mysql_temp')->table('sites')
                ->where('creator_id', '=', $users[0]->id)
                ->where('status', '=', $status)->count();

            // Отправка запроса на получение сайтов.
            $response = $this->ajax('GET', '/api/temp/sites', ['status' => $status], [], ['auth_current' => $users[0]->id]);
            $response->assertJsonCount($site_count, 'data');

        }

        // Отправка запроса от имени пользователя, имеющего разрешение на просмотр сайтов.
        $site_count_all = DB::connection('mysql_temp')->table('sites')->count();
        $response = $this->ajax('GET', '/api/temp/sites', ['all' => true], [], ['auth' => 'temp.sites.index']);
        $response->assertJsonCount($site_count_all, 'data');

    }

    /**
     * Отправка сайта на модерацию.
     */
    public function testSendToModeration()
    {

        Storage::fake('public');
        factory(Tag::class, 3)->create();
        factory(Language::class, 2)->create();
        factory(Type::class, 2)->create();
        $user = factory(User::class)->create();
        factory(Link::class, 1)->create(['status' => Link::HOLDED]);
        $site = factory(Site::class)->create(['status' => Site::CREATED]);

        // Отправка запроса на модерацию. Данных у сайта нет - должна быть ошибка.
        $response = $this->ajax('POST',"/api/temp/sites/{$site->id}/moderation", [], [], ['auth_current' => $user->id]);
        $response->assertStatus(200)->assertJson(['status' => false]);

        // Обновление данных сайта.
        $this->ajax('PUT',"/api/temp/sites/{$site->id}", [
            'languages' => [1, 2],
            'tags'      => [1, 2, 3],
            'images'    => [
                UploadedFile::fake()->image('thumbnail1.jpg'),
                UploadedFile::fake()->image('thumbnail.jpg')
            ],
            'rating'   => 6.5
        ], [], ['auth_current' => $user->id]);

        // Отправка запроса на модерацию. Данных у сайта нет - должна быть ошибка.
        $response = $this->ajax('POST',"/api/temp/sites/{$site->id}/moderation", [], [], ['auth_current' => $user->id]);
        $response->assertJson([
            'id' => $site->id,
            'status' => Site::MODERATION,
            'link' => [
                'status' => Link::HOLDED
            ]
        ]);
    }

    /**
     * Создание thumbnail модратором.
     * Создание сайта, обновление его (внесение изображений.)
     * Создание миниатюры.
     * Проверка существоания записи в базе данных и существования изображения в хранилище.
     */
    public function testCreationThumbnail()
    {
        Storage::fake('public');
        factory(Tag::class, 3)->create();
        factory(Language::class, 2)->create();
        factory(Type::class, 2)->create();
        factory(Link::class, 1)->create(['status' => Link::HOLDED]);
        $user = factory(User::class)->create();
        $site = factory(Site::class)->create(['status' => Site::MODERATION, 'creator_id' => $user->id]);
        $link = $site->link;

        // Обновление сайта.
        $update_response = $this->ajax('PUT',"/api/temp/sites/{$site->id}", [
            'languages' => [1, 2],
            'tags'      => [1, 2, 3],
            'images'    => [
                UploadedFile::fake()->image('thumbnail1.jpg'),
                UploadedFile::fake()->image('thumbnail.jpg')
            ],
            'rating'   => 6.5
        ], [], ['auth_current' => $user->id]);

        // Запрос от имени модератора сайта на добавление миниатюрного изображения.
        $image_id = $update_response->decodeResponseJson()['images']['data'][0]['id'];
        $response = $this->ajax('POST', "/api/temp/sites/{$site->id}/thumbnail", [
            'image_id' => $image_id
        ], [], ['auth' => 'temp.sites.moderation']);

        // Проверка существования записи thumbnail у сайта.
        $site = Site::findOrFail($site->id);
        $this->assertTrue((bool) $site->thumbnail);

        // Проверка существования изображения в хранилище.
        $exist_thumbnail = Storage::disk('public')->exists("{$site->hash}/{$site->thumbnail}");
        $this->assertTrue($exist_thumbnail);

    }

    /*
     * Публикация сайта.
     * Создание локального сайта, дополнение его изображениями и основными данными.
     * Проверка существования изображений в папке сервера.
     * Отправка запроса на публикацию сайта.
     * Проверка отсутствия изображений в старой папке (public)
     * Проверка существования изображений в новой папке (s3)
     * Проверка удаления локального сайта.
     * Проверка создания публичного сайта.
     * Проверка соответствия данных.
     * Очистка созданных файлов.
     */
    public function testPublication()
    {

        factory(Tag::class, 3)->create();
        factory(Language::class, 2)->create();
        factory(Type::class, 2)->create();
        factory(Link::class, 1)->create(['status' => Link::HOLDED]);
        $user = factory(User::class)->create();
        $site = factory(Site::class)->create(['status' => Site::MODERATION, 'creator_id' => $user->id]);
        $link = $site->link;

        // Обновление сайта.
        $response = $this->ajax('PUT',"/api/temp/sites/{$site->id}", [
            'languages' => [1, 2],
            'tags'      => [1, 2, 3],
            'images'    => [
                UploadedFile::fake()->image('thumbnail1.jpg'),
                UploadedFile::fake()->image('thumbnail.jpg')
            ],
            'rating'   => 6.5
        ], [], ['auth_current' => $user->id]);

        // Запрос от имени модератора сайта на добавление миниатюрного изображения.
        $image_id = $response->decodeResponseJson()['images']['data'][0]['id'];
        $this->ajax('POST', "/api/temp/sites/{$site->id}/thumbnail", [
            'image_id' => $image_id
        ], [], ['auth' => 'temp.sites.moderation']);

        // Отправка запроса на confirm.
        // Происходит публикация сайта на public.
        $response = $this->ajax('POST',"/api/temp/sites/{$site->id}/confirm", ['value' => true], [], ['auth' => 'temp.sites.moderation']);
        $response->assertJson([
            'status' => true
        ]);

        // Проверка баланса пользователя.
        $user->refresh();
        $this->assertTrue((bool)$user->balance == config('service-pricing.site_creation_price'));

        // Проверка статуса ссылки.
        $link->refresh();
        $this->assertTrue($link->status == Link::PUBLISHED);

        // Проверка отсутствия папки изображений сайта на сервере.
        $this->assertTrue(!Storage::disk('public')->exists($site->hash));

        // Проверка сушествования папки изображений сайта и их количества на удаленном сервере, от main.
        $this->assertTrue(Storage::disk('s3')->exists($site->hash));
        $this->assertCount(3, Storage::disk('s3')->files($site->hash));

        // Проверка отсутствия строки в базе данных mysql_temp sites
        $row = DB::connection('mysql_temp')->table('sites')->find($site->id);
        $this->assertTrue(!(bool) $row);

        // Проверка существования сайта в базе данных mysql_public sites
        $row = DB::connection('mysql_public')->table('sites')->where('url', '=', $site->link->url)->first();
        $this->assertTrue((bool) $row);

        // Проверка соответствий данных между старым и новым сайтом.
        $response = $this->ajax('GET', "/api/main/sites/{$row->id}", [], [], ['auth' => 'main.sites.show']);
        $response->assertJsonCount(2, 'languages.data');
        $response->assertJsonCount(3, 'tags.data');
        $response->assertJsonCount(2, 'images.data');

        // Удаление созданной папки с файлами на s3.
        Storage::disk('s3')->deleteDirectory($response->decodeResponseJson()['hash']);
        Storage::disk('public')->deleteDirectory($response->decodeResponseJson()['hash']);

    }

    /**
     * Откат сайта с модерации на доработку.
     * Изменение статуса сайта: возврат на статус Site::CREATED
     */
    public function testRollback()
    {

        Storage::fake('public');
        factory(Tag::class, 3)->create();
        factory(Language::class, 2)->create();
        factory(Type::class, 2)->create();
        factory(Link::class, 1)->create(['status' => Link::HOLDED]);
        $site = factory(Site::class)->create(['status' => Site::MODERATION]);

        // Обновление сайта.
        // Чтобы качественно откатить сайт с модерации - необходимо проверить существование данных в системе.
        // Обновление сайта, присваивая ему изображения, которые потом хранятся в файловой системе.
        // Проверка на существование.
        // Проверка изменения ссылки.
        $this->ajax('PUT',"/api/temp/sites/{$site->id}", [
            'images' => [
                UploadedFile::fake()->image('main1.jpg'),
                UploadedFile::fake()->image('main2.png')
            ],
            'languages' => [1, 2],
            'tags'      => [1, 2, 3],
            'thumbnail' => UploadedFile::fake()->image('thumbnail.jpg'),
            'rating'    => 6.5,
        ], [], ['auth' => 'admin']);

        // Отправка запроса и проверка данных.
        $response = $this->ajax('POST',"/api/temp/sites/{$site->id}/confirm", ['value' => false], [], ['auth' => 'temp.sites.moderation']);
        $response->assertJson([
            'status' => true
        ]);

        // Обновление модели сайта.
        $site->refresh();

        // Существуют ли файлы ?
        $this->assertTrue((bool) count(Storage::disk('public')->files($site->hash)));

        // Изменился ли статус у ссылки?
        $this->assertTrue($site->link->status == Link::HOLDED);

        // Какой статус сайта?
        $this->assertTrue($site->status == Site::CREATED);
    }

}
