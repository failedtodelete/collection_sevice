<?php

namespace App\Http\Controllers\API\Main;

use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Facades\Main\MainTagServiceFacade as TagService;
use App\Transformers\Main\MainTagTransformer;

class TagController extends Controller
{

    /**
     * Отображение всех тегов.
     * Если пришел параметр search - получение только тех тегов,
     * в которых встречается данная подстрока.
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $tags = $request->search ? TagService::where('name', 'LIKE', "%{$request->search}%")->get() : TagService::all();
        return fractal()
            ->collection($tags)
            ->transformWith(new MainTagTransformer())
            ->toJson();
    }

    /**
     * Создание тега.
     * Все переданные имена будут переведены в нижний регистр.
     * @param Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $tag = TagService::store($request->all());
        return fractal()
            ->item($tag)
            ->transformWith(new MainTagTransformer())
            ->toJson();
    }

    /**
     * Получение тега.
     * @param $id
     * @return string
     */
    public function show($id)
    {
        $tag = TagService::findOrFail($id);
        return fractal()
            ->item($tag)
            ->transformWith(new MainTagTransformer())
            ->toJson();
    }

    /**
     * Обновление тега.
     * @param Request $request
     * @param $id
     * @return string
     */
    public function update(Request $request, $id)
    {
        $tag = TagService::update($request->all(), $id);
        return fractal()
            ->item($tag)
            ->transformWith(new MainTagTransformer())
            ->toJson();
    }

    /**
     * Удаление тега.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return Response::json([
            'status' => (boolean) TagService::delete($id)
        ]);
    }

}
