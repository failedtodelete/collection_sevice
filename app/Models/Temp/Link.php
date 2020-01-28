<?php

namespace App\Models\Temp;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{

    protected $connection = "mysql_temp";

    /**
     * @var array
     */
    protected $fillable = [
        'url', 'creator_id', 'moderator_id', 'type_id', 'status'
    ];

    CONST MODERATION = 0;   // Ссылка на этапе модерации.
    CONST CONFIRMED = 1;    // Ссылка подтверждена модератором.
    CONST CANCELED = 2;     // Ссылка заблокирована или отклонена модератором.
    CONST PUBLISHED = 3;    // Ссылка опубликована.
    CONST HOLDED = 4;       // Ссылка заблокирована.

    /**
     * Получение пользователя, создавшего ссылку.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Получение пользователя, одобрившего ссылку.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Получение типа ссылки.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Получение сайта текущей ссылки.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function site()
    {
        return $this->hasOne(Site::class);
    }

}
