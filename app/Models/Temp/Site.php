<?php

namespace App\Models\Temp;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{

    protected $connection = 'mysql_temp';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'link_id', 'rating', 'thumbnail', 'hash', 'creator_id', 'status'
    ];

    /**
     * Available attributes.
     * @var array
     */
    protected $appends = [
        'make_moderation'
    ];

    CONST CREATED = 1;      // Сайт на этапе создания.
    CONST MODERATION = 2;   // Сайт на этапе модерации (подтверждения модератором).


    /**
     * Получение возможности отправки сайта на модерацию.
     * @param $key
     * @return bool
     */
    public function getMakeModerationAttribute($key)
    {
        if (!$this->rating) return false;
        if (!$this->tags->count()) return false;
        if (!$this->languages->count()) return false;
        if ($this->status !== Site::CREATED) return false;
        return true;
    }


    /**
     * Получение ссылки текущего сайта.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function link()
    {
        return $this->belongsTo(Link::class);
    }

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

    /**
     * Получение пользователя, создавшего сайт.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

}
