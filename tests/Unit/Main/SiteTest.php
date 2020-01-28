<?php

namespace Tests\Unit\Main;

use App\Models\Main\Site;
use App\Models\Main\Type;
use Tests\TestCase;

class SiteTest extends TestCase
{

    /**
     * Получение всех сайтов.
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function testIndexSite()
    {
        // Создание сайтов.
        factory(Type::class, 3)->create();
        factory(Site::class, 5)->create();

        // Отправка запроса
        $response = $this->ajax('GET', 'api/main/sites', [], [], ['auth' => 'main.sites.index']);
        return $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'url', 'rating', 'thumbnail', 'hash', 'type'
                    ]
                ]
            ]);
    }

}
