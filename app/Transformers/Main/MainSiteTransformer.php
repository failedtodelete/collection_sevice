<?php

namespace App\Transformers\Main;

use App\Traits\Helper;
use League\Fractal\TransformerAbstract;
use App\Models\Main\Site;

class MainSiteTransformer extends TransformerAbstract
{

    use Helper;

    protected $defaultIncludes = [
        'type', 'images', 'languages', 'tags'
    ];


    /**
     * A Fractal transformer
     * @param Site $site
     * @return array
     */
    public function transform(Site $site)
    {
        return [
            'id'        => (int) $site->id,
            'url'       => (string) $site->url,
            'rating'    => (float) $site->rating,
            'hash'      => (string) $site->hash,
            'thumbnail' => (string) $this->full_path($site->hash, $site->thumbnail)
        ];
    }

    /**
     * Получение языков текущего сайта.
     * @param Site $site
     * @return \League\Fractal\Resource\Collection
     */
    public function includeLanguages(Site $site)
    {
        return $this->collection($site->languages, new MainLanguageTransformer());
    }

    /**
     * Получение тегов текущего сайта.
     * @param Site $site
     * @return \League\Fractal\Resource\Collection
     */
    public function includeTags(Site $site)
    {
        return $this->collection($site->tags, new MainTagTransformer());
    }

    /**
     * Получение типа текущего сайта.
     * @param Site $site
     * @return \League\Fractal\Resource\Item
     */
    public function includeType(Site $site)
    {
        return $this->item($site->type, new MainTypeTransformer());
    }

    /**
     * Получение изображений текущего сайта.
     * @param Site $site
     * @return \League\Fractal\Resource\Collection
     */

    public function includeImages(Site $site)
    {
        return $this->collection($site->images, new MainImageTransformer());
    }

}
