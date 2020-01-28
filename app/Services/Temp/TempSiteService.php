<?php

namespace App\Services\Temp;

use App\Events\ConfirmationSite;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Facades\Main\MainSiteServiceFacade as MainSiteService;
use App\Facades\Temp\TempLanguageServiceFacade as LanguageService;
use App\Facades\Temp\TempImageServiceFacade as TempImageService;
use App\Facades\Temp\TempTagServiceFacade as TagService;
use App\Facades\Temp\LinkServiceFacade as LinkService;
use App\Exceptions\CustomException;
use App\Models\Temp\Site;
use App\Models\Temp\Link;

class TempSiteService extends BaseService
{

    /**
     * SiteService constructor.
     * @param Site $model
     */
    public function __construct(Site $model)
    {
        parent::__construct($model);
    }

    /**
     * Создание пустой модели сайта.
     * @param $data
     * @return mixed
     * @throws CustomException
     * @throws ValidationException
     */
    public function store($data)
    {
        $rules = [
            'link_id' => 'required|numeric|exists:mysql_temp.links,id'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Получение ссылки.
        $link = LinkService::findOrFail($data['link_id']);

        // Проверка доступности ссылки.
        if ($link->status !== Link::CONFIRMED) throw new CustomException('Ссылка имеет недопустимый статус');

        // Получение уникального идентификатора будущего сайта.
        $hash = $this->get_random_text(12);

        return Site::create([
            'link_id' => $link->id,
            'hash' => $hash,
            'creator_id'=> Auth::id(),
            'status' => Site::CREATED
        ]);
    }

    /**
     * Обновление / изменение сайта
     * @param $data
     * @param $id
     * @return mixed
     * @throws CustomException
     * @throws ValidationException
     */
    public function update($data, $id)
    {
        $rules = [
            'tags'      => 'nullable|array',
            'languages' => 'nullable|array',
            'rating'    => 'nullable|between:0,10',
            'images'    => 'nullable|array',
                'images.*' => 'required|file|mimes:jpg,png,jpeg'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Получение сайта.
        $site = self::findOrFail($id);

        try {

            DB::connection('mysql_temp')->beginTransaction();

            // Обновление тегов у сайта.
            if (key_exists('tags', $data)) {
                // Отсоединение всех тегов
                $site->tags()->detach();
                // Перебор и присвоение тегов.
                if (count($data['tags'])) {
                    $tags = TagService::whereIn('id', $data['tags'])->get();
                    foreach($tags as $tag) {
                        $site->tags()->attach($tag);
                    }
                }
            }

            // Обновление языков у сайта.
            if (key_exists('languages', $data)) {
                // Отсоединение всех языков..
                $site->languages()->detach();
                // Перебор и присвоение языков.
                if (count($data['languages'])) {
                    $languages = LanguageService::whereIn('id', $data['languages'])->get();
                    foreach($languages as $language) {
                        $site->languages()->attach($language);
                    }
                }
            }

            // Обновление рейтинга
            if (key_exists('rating', $data)) $site->rating = $data['rating'];

            // Обновление изображений сайта.
            if (key_exists('images', $data)) {
                // Удаление всех изображений
                $site->images()->delete();
                // Создание, загрузка и присвоего изображений сайту.
                // Получение рандомного имени файла с использованием настоящего расширения.
                // Создание сущности изображения и загрузка самого файла в хранилище.
                foreach($data['images'] as $image) {
                    // Создание изображения на удаленном сервере.
                    $image_url = TempImageService::upload($image, $site->hash, null, 'public');
                    // Ссоздание сущности и сохранения файла в хранилище.
                    $site->images()->create(['url' => "{$image_url}"]);
                }

                // Удаление лишних изображений из хранилища сайта.
                self::delete_unused_images($site->id);
            }

            // Сохранение сайта в базе данных.
            $site->save();

        } catch (\Exception $e) {
            DB::connection('mysql_temp')->rollBack();
            throw $e;
        }

        // Возврат обновленной модели.
        DB::connection('mysql_temp')->commit();
        return $site->refresh();

    }

    /**
     * Создание миниатюрного изображения из изображения сайта.
     * @param $id
     * @param $image_id
     * @return mixed
     * @throws CustomException
     */
    public function thumbnail($id, $image_id)
    {
        // Получение сайта.
        $site = self::findOrFail($id);

        // Получение изображения сайта по идентификатору.
        $image = $site->images()->find($image_id);
        if (!$image) throw new CustomException("Изображение {$image_id} не найдено у сайта {$site->id}");

        // Создание миниатюрного изображения (thumbnail)
        // Возвращает имя изображения
        $thumbnail = TempImageService::thumbnail($image, $site->hash);

        // Сохранение названия изображения в аттрибуте сайта.
        $site->thumbnail = $thumbnail;
        $site->save();

        // Очистка директории он лишних файлов (изображений)
        self::delete_unused_images($site->id);

        return $site;
    }

    /**
     * Подтверждение сайта модератором.
     * @param $id
     * @param $value
     * @return mixed
     * @throws CustomException
     */
    public function confirm($id, $value)
    {
        return $value ? $this->publication($id) : $this->rollback($id);
    }

    /**
     * Публикая temp сайта.
     * @param $id
     * @return mixed
     * @throws CustomException
     */
    protected function publication($id)
    {

        // Получение локального сайта.
        $site = self::findOrFail($id);

        // Формирование массива изображений.
        $images = [];
        foreach($site->images as $image) array_push($images, $image->url);

        // todo Проверка возможности публикации
        // todo Существуют ли все необходимые поля и данные для осуществления публикации.


        try {

            // Начало транзакций для двух баз данных.
            DB::connection('mysql_temp')->beginTransaction();
            DB::connection('mysql_public')->beginTransaction();

            // Создание публичного сайта.
            $main_site = MainSiteService::store([
                'url'       => $site->link->url,
                'thumbnail' => $site->thumbnail,
                'tags'      => $site->tags()->pluck('name')->toArray(),
                'images'    => $images,
                'languages' => $site->languages()->pluck('name')->toArray(),
                'type'      => $site->link->type->name,
                'rating'    => $site->rating,
                'hash'      => $site->hash
            ]);

            if ($main_site) {

                // Удаление локального сайта.
                $site->delete();

                // Изменение статуса ссылки на [PUBLISHED]
                $site->link->update(['status' => Link::PUBLISHED]);

                // Пополнение баланса пользователя за созданный сайт.
                event(new ConfirmationSite($site));
            }


        } catch (\Exception $e) {
            DB::connection('mysql_temp')->rollBack();
            DB::connection('mysql_public')->rollBack();
            throw $e;
        }

        DB::connection('mysql_temp')->commit();
        DB::connection('mysql_public')->commit();
        return $site;
    }

    /**
     * Отправка сайта на статус модерации.
     * @param $id
     * @return mixed
     * @throws CustomException
     */
    public function moderation($id)
    {
        // Получение сайта.
        $site = self::findOrFail($id);
        if ($site->status !== Site::CREATED) throw new CustomException('Недопустимый статус сайта');
        if (!$site->make_moderation) throw new CustomException('Невозможно отправить сайт на модерацию так как он не заполнен');

        // Присвоение статуса и возврат сайта.
        $site->status = Site::MODERATION;
        $site->save();
        return $site;
    }

    /**
     * Возврат сайта на статус создания.
     * Используется, когда модератор отклоняет сайт, присланный на модерацию.
     * @param $id
     * @return mixed
     * @throws CustomException
     */
    protected function rollback($id)
    {
        $site = self::findOrFail($id);
        $site->status = Site::CREATED;
        $site->save();
        return $site;
    }

    /**
     * Удаление не использованных изображений сайта.
     * Получение существующих изображений сайта на текущий момент,
     * формирование полного пути каждого изображения, включая hash.
     * Перебор всех изображений в папке сайта и удаление всех файлов, пути которых отсутствуют в массиве.
     * @param $id
     * @throws CustomException
     */
    public function delete_unused_images($id)
    {

        // Получение сайта.
        $site = self::findOrFail($id);

        // Массив существующих изображений, которые удалять нельзя.
        // example: [site2hash231j2941/sd2ah2gs2ne.jpeg, ...]
        $needed_files = [];

        // Перебор изображений сайта и внедрение в конечный массив.
        if ($site->images->count()) {
            $needed_files = array_map(function($image) use ($site) {
                return "{$site->hash}/{$image['url']}";
            }, $site->images->toArray());
        }

        // Добавление миниатюры.
        if ($site->thumbnail) {
            array_push($needed_files, "{$site->hash}/{$site->thumbnail}");
        }

        // Удаление не существующих изображений сайта.
        $exist_files = Storage::disk('public')->files($site->hash);
        if (count($exist_files)) {
            foreach($exist_files as $file) {
                if (!in_array($file, $needed_files)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
    }

}
