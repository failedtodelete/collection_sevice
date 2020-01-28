<?php

namespace App\Transformers\Temp;

use App\Traits\Helper;
use App\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;
use App\Models\Temp\Site;

class TempSiteTransformer extends TransformerAbstract
{

    use Helper;

    protected $defaultIncludes = [
        'link', 'images', 'languages', 'tags', 'creator'
    ];


    /**
     * A Fractal transformer
     * @param Site $site
     * @return array
     */
    public function transform(Site $site)
    {
        return [
            'id'        => (int)    $site->id,
            'rating'    => (float)  $site->rating,
            'hash'      => (string) $site->hash,
            'thumbnail' => $site->thumbnail,
            'status'    => (int)    $site->status,
            'disk'      => (string) 'temp',
            'actions' => [
                'make_moderation' => (bool) $site->make_moderation
            ]
        ];
    }

    /**
     * Получение языков текущего сайта.
     * @param Site $site
     * @return \League\Fractal\Resource\Collection
     */
    public function includeLanguages(Site $site)
    {
        return $this->collection($site->languages, new TempLanguageTransformer());
    }

    /**
     * Получение тегов текущего сайта.
     * @param Site $site
     * @return \League\Fractal\Resource\Collection
     */
    public function includeTags(Site $site)
    {
        return $this->collection($site->tags, new TempTagTransformer());
    }

    /**
     * Получение ссылки текущего сайта.
     * @param Site $site
     * @return \League\Fractal\Resource\Item
     */
    public function includeLink(Site $site)
    {
        return $this->item($site->link, new LinkTransformer());
    }

    /**
     * Получение изображений текущего сайта.
     * @param Site $site
     * @return \League\Fractal\Resource\Collection
     */

    public function includeImages(Site $site)
    {
        return $this->collection($site->images, new TempImageTransformer());
    }

    /**
     * Получение пользователя, создавшего сайт.
     * @param Site $site
     * @return \League\Fractal\Resource\Item
     */
    public function includeCreator(Site $site)
    {
        return $this->item($site->creator, new UserTransformer());
    }

}
