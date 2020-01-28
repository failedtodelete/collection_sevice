<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{

    protected $connection = 'mysql_public';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'url', 'type_id', 'rating', 'thumbnail', 'hash'
    ];

    /**
     * Получение типа сайта
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Получение изображений сайта
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Получение тегов текущего сайта
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'site_tag');
    }

    /**
     * Получение языков текущего сайта
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'site_language');
    }

}
