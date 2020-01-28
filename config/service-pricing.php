<?php

return [
    /*
     * The default serializer to be used when performing a transformation. It
     * may be left empty to use Fractal's default one. This can either be a
     * string or a League\Fractal\Serializer\SerializerAbstract subclass.
     */
    'link_creation_price' => env('LINK_CREATION_PRICE', 5),
    'site_creation_price' => env('SITE_CREATION_PRICE', 10)
];
