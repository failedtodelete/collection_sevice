<?php

namespace App\Services\Temp;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\CustomException;
use App\Services\BaseService;
use App\Models\Temp\Image;
use Intervention\Image\Facades\Image as InterventionImage;

class TempImageService extends BaseService
{

    protected $disk = 'public';

    /**
     * ImageService constructor.
     * @param Image $model
     */
    public function __construct(Image $model)
    {
        parent::__construct($model);
    }

    /**
     * Компрессия изображения
     * Если передана длина (width) и НЕ передана высота (height) - обработка изображения пропорционально длине
     * Если переданное изображение имеет расширение PNG - quality недоступен
     * Если расширение не передано - по умолчанию используется JPG
     * @param $file
     * @param null $width
     * @param null $height
     * @param int $quality
     * @param null $extension
     * @return \Intervention\Image\Image
     */
    public function compression($file, $width = null, $height = null, $quality = 100, $extension = NULL)
    {

        // Инициализация изображения
        $image = InterventionImage::make($file);

        // Обрезка.
        if ($width) {
            if ($height) $image->resize($width, $height);
            else $image->resize($width, NULL, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        // Преобразование расширения в JPG если оно не передано
        if ($extension == NULL) $extension = 'jpg';

        // Установка качества и возврат изображения.
        return $image->encode($extension ? $extension : $file->getClientOriginalExtension(), $quality);

    }

    /**
     * @param $image
     * @param $hash
     * @param null $name
     * @param int $length
     * @param string $disk
     * @return string|null
     * @throws CustomException
     */
    public function upload($image, $hash, $name = null, $disk = 'public')
    {

        // Проверка файла.
        if (!is_file($image)) throw new CustomException('Is not file');

        // Получение названия файла вместе с расширением.
        // Сохранение файла в хранилище.
        if (!$name) $name = $this->get_random_text(7) . '.' . $image->getClientOriginalExtension();
        Storage::disk($disk)->put("{$hash}/{$name}", file_get_contents($image), $disk);
        return $name;
    }

    /**
     * Создание миниатюрного изображения для сайта (thumbnail)
     * @param Image $image
     * @param $hash
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function thumbnail(Image $image, $hash)
    {

        // Получение исходного изображения.
        $source_image = Storage::disk('public')->get("{$hash}/{$image->url}");

        // Генерация уникального имени изображения.
        $thumbnail_name = $this->get_random_text(7) . '.' . $this->getExtension($image->url);

        // Сохранение изображения в папке сайта.
        Storage::disk('public')->put("{$hash}/{$thumbnail_name}", $source_image);

        // Возврат названия изображения.
        return $thumbnail_name;
    }

    /**
     * Получение расширения изображения.
     * @param $name
     * @return mixed
     */
    public function getExtension($name)
    {
        $arr = explode('.', $name);
        return $arr[count($arr) - 1];
    }

}
