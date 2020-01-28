<?php

namespace App\Services\Main;

use App\Models\Main\Tag;
use App\Models\Main\Type;
use App\Services\BaseService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MainTypeService extends BaseService
{

    /**
     * TypeService constructor.
     * @param Type $model
     */
    public function __construct(Type $model)
    {
        parent::__construct($model);
    }

    /**
     * Создание типа.
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function store($data)
    {
        $rules = [
            'name'  => 'required|string|unique:mysql_public.types,name'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Создание модели тега и возврат.
        return Type::create(['name' => $data['name']]);
    }


}
