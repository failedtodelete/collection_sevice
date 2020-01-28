<?php

namespace App\Models\Temp;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    public $timestamps      = false;
    protected $connection   = 'mysql_temp';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Получение сайтов текущего тега
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_tag');
    }

}
