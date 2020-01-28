<?php

namespace App\Transformers\Temp;

use App\Models\Temp\Type;
use League\Fractal\TransformerAbstract;

class TempTypeTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'sites'
    ];

    /**
     * A Fractal transformer
     * @param Type $type
     * @return array
     */
    public function transform(Type $type)
    {
        return [
            'id'    => (int) $type->id,
            'name'  => (string) $type->name,
        ];
    }

    /**
     * Получение сайтов текущего типа.
     * @param Type $type
     * @return \League\Fractal\Resource\Collection
     */
    public function includeSites(Type $type)
    {
        return $this->Collection($type->sites, new TempSiteTransformer());
    }

}
