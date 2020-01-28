<?php

namespace App\Transformers;

use App\Models\UserStatus;
use League\Fractal\TransformerAbstract;

class UserStatusTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'users'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(UserStatus $status)
    {
        return [
            'name' =>           (string) $status->name,
            'display_name' =>   (string) $status->display_name
        ];
    }

    /**
     * Получение пользователей текущего статуса.
     * @param UserStatus $status
     * @return \League\Fractal\Resource\Collection
     */
    public function includeUsers(UserStatus $status)
    {
        return $this->collection($status->users, new UserTransformer());
    }

}
