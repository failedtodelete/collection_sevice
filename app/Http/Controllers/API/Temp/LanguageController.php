<?php

namespace App\Http\Controllers\API\Temp;

use App\Http\Controllers\Controller;

use App\Facades\Temp\TempLanguageServiceFacade as LanguageService;
use App\Transformers\Temp\TempLanguageTransformer;

class LanguageController extends Controller
{

    /**
     * Получение всех языков.
     * @return string
     */
    public function index()
    {
        $languages = LanguageService::all();
        return fractal()
            ->collection($languages)
            ->transformWith(new TempLanguageTransformer())
            ->toJson();
    }

}
