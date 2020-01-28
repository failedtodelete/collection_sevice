<?php

namespace Tests\Unit\Temp;

use App\Models\Temp\Type;
use Tests\TestCase;

class TypeTest extends TestCase
{

    /**
     * Получение всех типов.
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function testIndexType()
    {

        // Создание языков.
        factory(Type::class, 2)->create();

        // Отправка запроса
        $response = $this->ajax('GET', 'api/temp/types', [], [], ['auth' => 'admin']);
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
