<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\PermissionType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PermissionService extends BaseService
{

    /**
     * PermissionService constructor.
     * @param Permission $model
     */
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    /**
     * Создание разрешения.
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function store($data)
    {
        $rules = [
            'type_id'       => 'required|numeric|exists:mysql_admin.permission_types,id',
            'name'          => 'required|string',
            'display_name'  => 'required|string',
            'description'   => 'nullable|string'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        return Permission::create($data);
    }

    /**
     * Создание типа разрешения.
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function permissionTypeStore($data)
    {
        $rules = [
            'name'          => 'required|string',
            'display_name'  => 'required|string',
            'description'   => 'nullable|string'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        return PermissionType::create($data);
    }

}
