<?php

namespace App\Services\Temp;

use App\Models\Temp\Type;
use App\Services\BaseService;

class TempTypeService extends BaseService
{

    /**
     * TypeService constructor.
     * @param Type $model
     */
    public function __construct(Type $model)
    {
        parent::__construct($model);
    }

}
