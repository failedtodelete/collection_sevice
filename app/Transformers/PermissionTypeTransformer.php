<?php

namespace App\Transformers;

use App\Models\PermissionType;
use League\Fractal\TransformerAbstract;

class PermissionTypeTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'permissions'
    ];

    /**
     * A Fractal transformer.
     * @param PermissionType $type
     * @return array
     */
    public function transform(PermissionType $type)
    {
        return [
            'name'          => (string) $type->name,
            'display_name'  => (string) $type->display_name,
            'description'   => (string) $type->description
        ];
    }

    /**
     * Получение разрешений текущего типа.
     * @param PermissionType $type
     * @return \League\Fractal\Resource\Collection
     */
    public function includePermissions(PermissionType $type)
    {
        return $this->collection($type->permissions, new PermissionTransformer());
    }

}
