<?php

namespace App\Http\Controllers\API\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Facades\Main\MainSiteServiceFacade as SiteService;
use App\Transformers\Main\MainSiteTransformer;
use App\Models\Main\Site;

class SiteController extends Controller
{

    /**
     * Получение всех сайтов системы.
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Проверка прав доступа.
        $this->authorize('index', Site::class);

        // Показать все сайты.
        $sites = SiteService::all();
        return fractal()
            ->collection($sites)
            ->transformWith(new MainSiteTransformer())
            ->toJson();
    }

    /**
     * Создание сайта.
     * @param Request $request
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {

        // Проверка прав доступа.
        $this->authorize('create', Site::class);

        $site = SiteService::store($request->all());
        return fractal()
            ->item($site)
            ->transformWith(new MainSiteTransformer())
            ->toJson();
    }

    /**
     * Найти сайт по ID. При нахождении коллекции выдаёт результат.
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, $id)
    {
        // Проверка прав доступа.
        $this->authorize('show', Site::class);

        $site = SiteService::findOrFail($id);
        return fractal()
            ->item($site)
            ->transformWith(new MainSiteTransformer())
            ->toJson();
    }

}
