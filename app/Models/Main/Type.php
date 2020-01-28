<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
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
     * Получение всех сайтов текущего типа
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sites()
    {
        return $this->hasMany(Site::class);
    }

}
