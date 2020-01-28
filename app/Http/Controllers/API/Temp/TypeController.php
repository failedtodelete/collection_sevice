<?php

namespace App\Http\Controllers\API\Temp;

use App\Http\Controllers\Controller;

use App\Facades\Temp\TempTypeServiceFacade as TypeService;
use App\Transformers\Temp\TempTypeTransformer;

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
            ->transformWith(new TempTypeTransformer())
            ->toJson();
    }

}
