<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{

    public $timestamps      = false;
    protected $connection   = 'mysql_public';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Получение сайтов текущего языка
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_language');
    }

}
