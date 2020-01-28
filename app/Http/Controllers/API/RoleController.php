<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Facades\RoleServiceFacade as RoleService;
use App\Transformers\RoleTransformer;
use App\Models\Role;

class RoleController extends Controller
{

    /**
     * Получение всех ролей системы.
     * @return mixed
     */
    public function index()
    {
        $roles = RoleService::all();
        return fractal()
            ->collection($roles)
            ->transformWith(new RoleTransformer())
            ->toJson();
    }

    /**
     * Переключение разрешения для роли.
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function toggle_permission(Request $request, $id)
    {

        // Авторизация действия.
        $this->authorize('update', Role::class);

        // Изменение разрешения для роли.
        $role = RoleService::toggle_permission($request->all(), $id);
        return fractal()
            ->item($role)
            ->transformWith(new RoleTransformer())
            ->toJson();
    }

}
