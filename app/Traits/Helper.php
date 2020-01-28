<?php namespace App\Traits;

trait Helper
{

    /**
     * Получение рандомной строки.
     * Передав $length - можно указать количество символов.
     * @param int $length
     * @return bool|string
     */
    public function get_random_text($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Получение названия передавамого класса (пути).
     * @param $class
     * @return string
     */
    public function classBasename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }

    /**
     * Форматирование ссылки.
     * @param $url
     * @return string
     */
    public function formatUrl($url)
    {
        if (strlen($url)) {
            $url = str_replace(' ', '', $url);
            if (substr($url, -1) == '/') {
                return $this->formatUrl(mb_substr($url, 0, -1));
            }
        }
        return $url;
    }

    /**
     * Получение полного пути файла из хранилища.
     * @param $hash
     * @param $url
     * @param string $disk
     * @return string
     */
    public function full_path($hash, $url, $disk = 's3')
    {
        return config('filesystems.disks.' . $disk . '.path') . $hash . '/' . $url;
    }

}
