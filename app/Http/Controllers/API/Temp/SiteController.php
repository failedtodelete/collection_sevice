<?php

namespace App\Http\Controllers\API\Temp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

use App\Facades\Temp\TempSiteServiceFacade as SiteService;
use App\Transformers\Temp\TempSiteTransformer;
use Illuminate\Support\Facades\Validator;
use App\Models\Temp\Site;

class SiteController extends Controller
{

    /**
     * Получение всех сайтов.
     * Если передан all = true - получение всех сайтов.
     * Если флаг не передан - получение сайтов текущего пользователя.
     * @param Request $request
     * @return string
     * @throws ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $rules = [
            'status' => 'nullable|numeric',
            'all' => 'nullable|boolean'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Получение всех сайтов или сайтов текущего пользователя
        // в зависимости от переданного аттрибута all
        if ($request->all) {

            // Проверка прав доступа.
            $this->authorize('index', Site::class);

            // Получение всех сайтов. (query)
            $sites = SiteService::where('id', '>', 0);
        } else $sites = SiteService::where('creator_id', '=', Auth::id());

        // Добавление запроса на статус если он передан в запросе.
        if ($request->status) $sites->where('status', '=', $request->status);
        return fractal()
            ->collection($sites->get())
            ->transformWith(new TempSiteTransformer())
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
        $this->authorize('create', Site::class);
        $site = SiteService::store($request->all());
        return fractal()
            ->item($site)
            ->transformWith(new TempSiteTransformer())
            ->toJson();
    }

    /**
     * Обновление/изменение сайта.
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $site = SiteService::update($request->all(), $id);
        $this->authorize('update', $site);
        return fractal()
            ->item($site)
            ->transformWith(new TempSiteTransformer())
            ->toJson();
    }

    /**
     * Получение сайта.
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, $id)
    {
        // Получение сайта и проверка прав доступа.
        $site = SiteService::findOrFail($id);
        $this->authorize('show', $site);
        return fractal()
            ->item($site)
            ->transformWith(new TempSiteTransformer())
            ->toJson();
    }

    /**
     * Отправка сайта на статус модерации.
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function moderation(Request $request, $id)
    {
        // Получение сайта и проверка прав доступа.
        // Отправка сайта на статус модерации.
        $site = SiteService::findOrFail($id);
        $this->authorize('update', $site);
        $site = SiteService::moderation($id);
        return fractal()
            ->item($site)
            ->transformWith(new TempSiteTransformer())
            ->toJson();
    }

    /**
     * Создание миниатюрного изображения сайта (thumbnail)
     * Пользователь: модерирующий сайт, перед публикацией должен выбрать миниатюру и создать.
     * Во входных параметрах передается идентификатор изображения сайта.
     * @param Request $request
     * @param $id
     * @return string
     * @throws ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function thumbnail(Request $request, $id)
    {
        $rules = [
            'image_id' => 'required|numeric|exists:mysql_temp.images,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Авторизация текущего действия.
        $this->authorize('moderation', Site::class);

        // Создание миниатюрного изображения и присвоение его сайту (thumbnail)
        $site = SiteService::thumbnail($id, $request->image_id);
        return fractal()
            ->item($site)
            ->transformWith(new TempSiteTransformer())
            ->toJson();
    }

    /**
     * Модерация сайта.
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function confirm(Request $request, $id)
    {
        $rules = [
            'value' => 'required|boolean',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Авторизация текущего действия.
        // Модерация сайта.
        $this->authorize('moderation', Site::class);
        $site = SiteService::confirm($id, $request->value);
        return Response::json([
            'status' => (bool) $site
        ]);
    }

}
