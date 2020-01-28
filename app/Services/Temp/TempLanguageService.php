<?php

namespace App\Services\Temp;

use App\Services\BaseService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Models\Temp\Language;

class TempLanguageService extends BaseService
{

    /**
     * LanguageService constructor.
     * @param Language $model
     */
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }

    /**
     * Создание языка.
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function store($data)
    {
        $rules = [
            'name' => 'required|string'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        return Language::create($data);
    }

}
