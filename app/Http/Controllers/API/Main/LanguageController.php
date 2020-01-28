<?php

namespace App\Http\Controllers\API\Main;

use App\Http\Controllers\Controller;

use App\Facades\Main\MainLanguageServiceFacade as LanguageService;
use App\Transformers\Main\MainLanguageTransformer;

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
            ->transformWith(new MainLanguageTransformer())
            ->toJson();
    }

}
