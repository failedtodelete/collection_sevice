<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    protected $connection = 'mysql_admin';

    CONST ACTIVE = 1;
    CONST INACTIVE = 2;
    CONST BLOCKED = 3;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'display_name'
    ];

    /**
     * Получение пользователей с текущим статусом.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'status_id');
    }

}
