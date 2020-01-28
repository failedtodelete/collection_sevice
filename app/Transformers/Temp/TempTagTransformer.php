<?php

namespace App\Transformers\Temp;

use App\Models\Temp\Tag;
use League\Fractal\TransformerAbstract;

class TempTagTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'sites'
    ];

    /**
     * A Fractal transformer
     * @param Tag $tag
     * @return array
     */
    public function transform(Tag $tag)
    {
        return [
            'id'    => (int) $tag->id,
            'name'  => (string) $tag->name,
        ];
    }

    /**
     * Получение сайтов текущего тега.
     * @param Tag $tag
     * @return \League\Fractal\Resource\Collection
     */
    public function includeSites(Tag $tag)
    {
        return $this->collection($tag->sites, new TempSiteTransformer());
    }

}
