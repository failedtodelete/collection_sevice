<?php

namespace App\Transformers\Main;

use App\Traits\Helper;
use League\Fractal\TransformerAbstract;

use App\Models\Main\Image;

class MainImageTransformer extends TransformerAbstract
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
            'id'   => (int) $image->id,
            'url'  => (string) $this->full_path($image->site->hash, $image->url),
        ];
    }

    /**
     * Получение сайта текущего фото.
     * @param Image $image
     * @return \League\Fractal\Resource\Item
     */
    public function includeSite(Image $image)
    {
        return $this->Item($image->site, new MainSiteTransformer());
    }

}
