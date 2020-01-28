<?php

namespace App\Http\Controllers\API\Temp;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Facades\Temp\LinkServiceFacade as LinkService;
use App\Transformers\Temp\LinkTransformer;
use App\Models\Temp\Link;
use App\Traits\Helper;

class LinkController extends Controller
{

    use Helper;

    /**
     * Создание ссылки.
     * @param Request $request
     * @return string
     * @throws ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {

        $rules = [
            'url' => 'required|url',
            'type_id'   => 'required|numeric|exists:mysql_temp.types,id'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Проверка прав доступа.
        $this->authorize('create', Link::class);

        // Преобразование ссылки в валидный вид.
        $request->merge(['url' => $this->formatUrl($request->url)]);

        // Создание доступной ссылки.
        $link = LinkService::store($request->all());
        return fractal()
            ->item($link)
            ->transformWith(new LinkTransformer())
            ->toJson();
    }

    /**
     * Подтверждение ссылки.
     * @param Request $request
     * @param $id
     * @return string
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
        $this->authorize('moderation', Link::class);

        // Модерация ссылки.
        $link = LinkService::confirm($id, $request->value);
        return fractal()
            ->item($link)
            ->transformWith(new LinkTransformer())
            ->toJson();
    }

    /**
     * Получение доступных ссылок.
     * @param Request $request
     * @return string
     */
    public function available(Request $request)
    {
        $links = LinkService::available();
        return fractal()
            ->collection($links)
            ->transformWith(new LinkTransformer())
            ->toJson();
    }

}
