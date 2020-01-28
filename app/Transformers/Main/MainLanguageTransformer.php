<?php

namespace App\Transformers\Main;

use App\Models\Main\Language;
use League\Fractal\TransformerAbstract;

class MainLanguageTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'sites'
    ];

    /**
     * A Fractal transformer
     * @param Language $language
     * @return array
     */
    public function transform(Language $language)
    {
        return [
            'id'       => (int) $language->id,
            'name'     => (string) $language->name,
        ];
    }

    /**
     * Получение сайтов текущего языка.
     * @param Language $language
     * @return \League\Fractal\Resource\Collection
     */
    public function includeSites(Language $language)
    {
        return $this->collection($language->sites, new MainSiteTransformer());
    }

}
