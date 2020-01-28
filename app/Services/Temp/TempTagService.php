<?php

namespace App\Services\Temp;

use App\Services\BaseService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Exceptions\CustomException;
use App\Models\Temp\Tag;

class TempTagService extends BaseService
{

    /**
     * TagService constructor.
     * @param Tag $model
     */
    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    /**
     * Создание тега.
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function store($data)
    {
        $rules = [
            'name'  => 'required|string|unique:mysql_temp.tags,name'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Создание модели тега и возврат.
        return Tag::create(['name' => mb_strtolower($data['name'])]);
    }

    /**
     * Обновление данных тега.
     * @param $data
     * @param $id
     * @return mixed
     * @throws CustomException
     * @throws ValidationException
     */
    public function update($data, $id)
    {
        $rules = [
            'name'  => 'required|string'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Получение теги и проверка доступности названия.
        $tag = self::findOrFail($id);
        if ($data['name'] !== $tag->name) {
            $exist_tag = (boolean) self::where('name', '=', $data['name'])->count();
            if ($exist_tag) throw new CustomException('Данное имя тега уже зарегистрированно');
        }

        $tag->name = strtolower($data['name']);
        $tag->save();
        return $tag;
    }

    /**
     * Удаление тега.
     * @param $id
     * @return bool
     * @throws CustomException
     */
    public function delete($id)
    {
        $tag = self::findOrFail($id);
        $tag->delete();
        return true;
    }

}
