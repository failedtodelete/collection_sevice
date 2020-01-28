<?php

namespace App\Transformers\Temp;

use App\Traits\Helper;
use League\Fractal\TransformerAbstract;

use App\Models\Temp\Image;

class TempImageTransformer extends TransformerAbstract
{

    use Helper;

    protected $availableIncludes = [
        'site'
    ];

    /**
     * A Fractal transformer
     * @param Image $image
     * @return array
     */
    public function transform(Image $image)
    {
        return [
            'id'        => (int)    $image->id,
            'url'       => (string) $image->url,
            'disk'      => (string) 'public'
        ];
    }

    /**
     * Получение сайта текущего фото.
     * @param Image $image
     * @return \League\Fractal\Resource\Item
     */
    public function includeSite(Image $image)
    {
        return $this->Item($image->site, new TempSiteTransformer());
    }

}
