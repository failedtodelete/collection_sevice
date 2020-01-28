<?php

namespace Tests\Unit\Temp;

use App\Models\Temp\Language;
use Tests\TestCase;

class LanguageTest extends TestCase
{

    /**
     * Получение всех языков.
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function testIndexLanguage()
    {

        // Создание языков.
        factory(Language::class, 2)->create();

        // Отправка запроса
        $response = $this->ajax('GET', '/api/temp/languages', [], [], ['auth' => 'agent']);
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

}
