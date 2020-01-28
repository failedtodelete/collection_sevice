<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $connection = 'mysql_admin';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'type_id', 'name', 'display_name', 'description', 'main'
    ];

    /**
     * Получение типа разрешения.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(PermissionType::class, 'type_id');
    }

    /**
     * Получение ролей текущего разрешения.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Permission::class);
    }

}
