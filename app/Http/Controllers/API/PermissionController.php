<?php namespace App\Http\Controllers\API;

use App\Facades\PermissionServiceFacade as PermissionService;
use App\Transformers\PermissionTransformer;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{

    /**
     * Получение всех ролей системы.
     * @return mixed
     */
    public function index()
    {

        $roles = PermissionService::all();
        return fractal()
            ->collection($roles)
            ->transformWith(new PermissionTransformer())
            ->toJson();
    }

}
