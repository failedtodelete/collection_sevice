<?php namespace App\Models;

use App\Models\Temp\Link;
use App\Models\Temp\Site;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, Notifiable;
    protected $connection = 'mysql_admin';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'balance', 'paid_out', 'status_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password', 'email_verified_at', 'remember_token', 'role_id'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Получение ролей пользователя.
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Получение локальных сайтов пользователя.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sites()
    {
        return $this->hasMany(Site::class, 'creator_id');
    }

    /**
     * Опубликованные ссылки пользователя.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function links()
    {
        return $this->hasMany(Link::class, 'creator_id');
    }

    /**
     * Получение истории баланса пользователя.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function balance_transactions()
    {
        return $this->hasMany(BalanceTransaction::class);
    }

    /**
     * Ссылки, которые находятся в работе у пользователя.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function available_links()
    {
        return $this->hasMany(Link::class, 'current_agent_id');
    }

    /**
     * Получение статуса пользователя.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(UserStatus::class, 'status_id');
    }

    /**
     * Получение доступа к разрешению роли пользователя.
     * @param $permission
     * @return bool
     */
    public function access($permission)
    {
        return (bool) $this->role->permissions
            ->where('name', $permission)
            ->first();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
