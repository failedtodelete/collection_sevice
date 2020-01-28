<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{

    protected $connection = 'mysql_public';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'url', 'site_id'
    ];

    /**
     * Получение сайта
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

}
