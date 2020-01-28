<?php

use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Authorize Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/

Route::group(['middleware' => ['auth:api'], 'namespace' => 'API'], function() {

    // ПРОФИЛЬ ТЕКУЩЕГО ПОЛЬЗОВАТЕЛЯ.
    Route::group(['prefix' => 'profile'], function() {
        Route::get('/', 'UserController@profile');                      // Просмотр профайла
    });

    // ПОЛЬЗОВАТЕЛИ
    Route::group(['prefix' => 'users'], function() {

        // РОЛИ И РАЗРЕШЕНИЯ
        Route::get('permissions', 'PermissionController@index');        // Просмотр всех разрешений
        Route::group(['prefix' => 'roles'], function() {
            Route::get('/', 'RoleController@index');                                        // Просмотр всех ролей
            Route::post('/{id}/toggle_permission', 'RoleController@toggle_permission');     // Добавление/удаление разрешения у роли
        });

        Route::get('', 'UserController@index');                         // Просмотр всех пользователей
        Route::post('', 'UserController@store');                        // Добавление пользователя
        Route::get('/{id}', 'UserController@show');                      // Просмотр пользователя
    });

    // СЕРВИСЫ
    Route::group(['prefix' => 'services', 'namespace' => 'Services'], function() {
        // Image service
        // Обработка изображений: сжимание в пропорция и без.
        // Изменение качества изображения и его размера.
        Route::group(['prefix' => 'image'], function() {
            Route::post('compression', 'ImageController@compression');  // Изменение размеров изображения
        });
    });

    // MAIN DATABASE ---------------------------------
    // Публичные сайты, языки, теги, изображения и типы.
    // Основные пользователи.
    Route::group(['prefix' => 'main', 'namespace' => 'Main'], function() {
        Route::group(['prefix' => 'sites'], function() {
            Route::get('', 'SiteController@index');                     // Получение всех сайтов
            Route::get('/{id}', 'SiteController@show');                 // Просмотр сайта
            Route::post('', 'SiteController@store');                    // Создание сайта
        });
        Route::resource('tags', 'TagController');                       // Просмотр всех тегов
        Route::get('languages', 'LanguageController@index');            // Просмотр всех языков
        Route::get('types', 'TypeController@index');                    // Просмотр всех типов
    });

    // TEMP DATABASE ----------------------------------
    // Локальные сайты (загружаемые пользователями) их языки, изображения и типы.
    // Агенты,  администраторы.
    Route::group(['prefix' => 'temp', 'namespace' => 'Temp'], function() {
        Route::group(['prefix' => 'sites'], function() {
            Route::get('',      'SiteController@index');                // Просмотр всех сайтов
            Route::post('',     'SiteController@store');                // Создание пустого сайта на основе ссылки
            Route::get('/{id}', 'SiteController@show');                 // Просмотр сайта
            Route::put('/{id}', 'SiteController@update');               // Обновление / изменение
            Route::post('/{id}/confirm', 'SiteController@confirm');     // Модерация сайта.
            Route::post('/{id}/thumbnail', 'SiteController@thumbnail'); // Создание миниатюры (обложки сайта)
            Route::post('/{id}/moderation', 'SiteController@moderation'); // Отправка сайта на статус модерации.

            Route::group(['prefix' => '{id}/images'], function() {
                Route::post('', 'SiteController@store_image');          // Добавление изображений сайта
                Route::delete('', 'SiteController@delete_image');       // Удаление изображений сайта
            });
        });

        Route::resource('tags', 'TagController');                       // Управление тегами [получение/просмотр/обновление/удаление]
        Route::get('languages', 'LanguageController@index');            // Просмотр всех языков сайтов
        Route::get('types', 'TypeController@index');                    // Просмотр всех типов сайтов

        Route::group(['prefix' => 'links'], function() {
            Route::post('', 'LinkController@store');                    // Создание ссылки
            Route::get('available', 'LinkController@available');        // Получение доступных ссылок
            Route::post('{id}/confirm', 'LinkController@confirm');      // Решение модератора на принятие или отказ ссылки
        });
    });

});

