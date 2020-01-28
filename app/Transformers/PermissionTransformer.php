<?php

namespace App\Transformers;

use App\Models\Permission;
use League\Fractal\TransformerAbstract;

class PermissionTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'type'
    ];

    /**
     * A Fractal transformer.
     * @param Permission $permission
     * @return array
     */
    public function transform(Permission $permission)
    {
        return [
            'id'            => (int) $permission->id,
            'name'          => (string) $permission->name,
            'display_name'  => (string) $permission->display_name,
            'description'   => (string) $permission->description
        ];
    }

    /**
     * Получение типа текущего разрешения.
     * @param Permission $permission
     * @return \League\Fractal\Resource\Item
     */
    public function includeType(Permission $permission)
    {
        return $this->item($permission->type, new PermissionTypeTransformer());
    }

}
