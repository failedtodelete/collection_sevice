<?php

namespace App\Services\Main;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Facades\Main\MainLanguageServiceFacade as LanguageService;
use App\Facades\Main\MainImageServiceFacade as ImageService;
use App\Facades\Main\MainTagServiceFacade as TagService;
use App\Facades\Main\MainTypeServiceFacade as TypeService;
use App\Models\Main\Site;

class MainSiteService extends BaseService
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
     * Создание сайта.
     * Перебор имеющихся массивов связующих элементов и присвоение сущностей к сайту.
     * Создание миниатюрного изображения (обложки)
     * Создание основных изображений и загрузка их в удаленное хранилище.
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function store($data)
    {

        $rules = [
            'url'       => 'required|url|unique:mysql_public.sites,url',
            'thumbnail' => 'required|string',   // Название изображения (само изображение находится на public диске)
            'tags'      => 'required|array',    // Теги, названия.
            'images'    => 'required|array',    // Изображения, в виде массив названий файлов (on public)
            'languages' => 'required|array',    // Языки, названия.
            'type'      => 'required|string',   // Тип, название.
            'rating'    => 'required|between:0,10', // Рейтинг
            'hash'      => 'required|string'        // Хеш сайта.
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        try {

            // Инициализация существующих изображений.
            $images = [];
            $thumbnail = Image::make(storage_path() . "/app/public/{$data['hash']}/{$data['thumbnail']}");
            foreach($data['images'] as $image) {
                $i = Image::make(storage_path() . "/app/public/{$data['hash']}/{$image}");
                array_push($images, $i);
            }

            // Проверка существования типа.
            // Если тип не найден - создание.
            $type = TypeService::where('name', '=', $data['type'])->first();
            if (!$type) $type = TypeService::store(['name' => $data['type']]);

            // Создание сайта.
            $site = new Site();
            $site->url = $data['url'];
            $site->type_id = $type->id;
            $site->rating = $data['rating'];
            $site->thumbnail = "{$thumbnail->filename}.{$thumbnail->extension}";
            $site->hash = $data['hash'];
            $site->save();

            // Перебор и присвоение тегов.
            foreach($data['tags'] as $tag) {
                $exist_tag = TagService::where('name', '=', $tag)->first();
                if (!$exist_tag) $exist_tag = TagService::store(['name' => $tag]);
                $site->tags()->attach($exist_tag);
            }

            // Перебор и присвоение языков.
            foreach($data['languages'] as $language) {
                $exist_language = LanguageService::where('name', '=', $language)->first();
                if (!$exist_language) $exist_language = LanguageService::store(['name' => $language]);
                $site->languages()->attach($exist_language);
            }

            // Создание изображений на диске.
            ImageService::upload($thumbnail->encode(), $data['hash'], $site->thumbnail);
            foreach ($images as $image) {
                ImageService::upload($image->encode(), $data['hash'], $image->filename);
                $site->images()->create(['url' => $image->filename]);
            }

            // Перезагрузка сайта со связями.
            $site->refresh();

        } catch (\Exception $e) {
            DB::connection('mysql_public')->rollBack();
            Storage::disk('s3')->deleteDirectory($data['hash']);
            throw $e;
        }

        DB::connection('mysql_public')->commit();
        return $site;

    }

}
