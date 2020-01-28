<?php

namespace App\Transformers\Temp;

use App\Models\Temp\Link;
use App\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class LinkTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'creator', 'moderator'
    ];

    protected $defaultIncludes = [
        'type'
    ];

    /**
     * A Fractal transformer.
     * @param Link $link
     * @return array
     */
    public function transform(Link $link)
    {
        return [
            'id'        => (int)        $link->id,
            'url'       => (string)     $link->url,
            'status'    => (int)        $link->status
         ];
    }

    /**
     * Добавление создателя.
     * @param Link $link
     * @return \League\Fractal\Resource\Item
     */
    public function includeCreator(Link $link)
    {
        return $this->item($link->creator, new UserTransformer());
    }

    /**
     * Добавление модератора.
     * @param Link $link
     * @return \League\Fractal\Resource\Item
     */
    public function includeModerator(Link $link)
    {
        return $this->item($link->moderator, new UserTransformer());
    }

    /**
     * Получение типа ссылки.
     * @param Link $link
     * @return \League\Fractal\Resource\Item
     */
    public function includeType(Link $link)
    {
        return $this->item($link->type, new TempTypeTransformer());
    }

}
