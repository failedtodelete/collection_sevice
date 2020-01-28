<?php

namespace Tests\Unit\Main;

use App\Models\Main\Tag;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TagTest extends TestCase
{

    /**
     * Получение всех тегов.
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function testIndexTag()
    {

        // Создание тегов.
        factory(Tag::class, 2)->create();

        // Отправка запроса
        $response = $this->ajax('GET', 'api/main/tags', [], [], ['auth' => 'admin']);
        return $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name'
                    ]
                ]
            ]);
    }

    /**
     * Поиск по всем тегам.
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function testIndexSearch()
    {

        // Создание тегов.
        factory(Tag::class, 200)->create()->pluck('id')->toArray();
        $available_tags = DB::connection('mysql_public')->table('tags')
            ->where('name', 'LIKE', '%as%')->get();

        // Отправка запроса на все теги.
        $response = $this->ajax('GET', 'api/main/tags?search=as', [], [], ['auth' => 'admin']);
        return $response
            ->assertStatus(200)
            ->assertJsonCount($available_tags->count(), 'data');
    }

    /**
     * Создание тега.
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function testCreateTag()
    {
        $data = ['name' => 'test123'];
        $response = $this->ajax('POST', 'api/main/tags', $data, [], ['auth' => 'admin']);
        return $response
            ->assertStatus(200)
            ->assertJson(['name' => 'test123']);
    }

    /**
     * Обновление / изменение тега.
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function testUpdateTag()
    {
        $parent = ['name' => 'test123'];
        $new = ['name' => '123test'];

        factory(Tag::class, 1)->create($parent);
        $response = $this->ajax('PUT', 'api/main/tags/1', $new, [], ['auth' => 'admin']);
        return $response
            ->assertStatus(200)
            ->assertJson(['name' => '123test']);
    }

    /**
     * Удаление тега.
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function testDeleteTag()
    {
        factory(Tag::class, 1)->create();
        $response = $this->ajax('DELETE', 'api/main/tags/1', [], [], ['auth' => 'admin']);
        return $response
            ->assertJson(['status' => true]);
    }

}
