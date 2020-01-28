<?php

namespace App\Http\Controllers\API\Main;

use App\Http\Controllers\Controller;

use App\Facades\Main\MainTypeServiceFacade as TypeService;
use App\Transformers\Main\MainTypeTransformer;

class TypeController extends Controller
{

    /**
     * Получение всех типов.
     * @return string
     */
    public function index()
    {
        $types = TypeService::all();
        return fractal()
            ->collection($types)
            ->transformWith(new MainTypeTransformer())
            ->toJson();
    }

}
