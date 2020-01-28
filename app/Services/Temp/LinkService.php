<?php

namespace App\Services\Temp;

use App\Services\BaseService;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Facades\UserServiceFacade as UserService;
use App\Events\ConfirmationLink;
use App\Models\Temp\Link;
use App\Traits\Helper;

class LinkService extends BaseService
{

    use Helper;

    /**
     * LinkService constructor.
     * @param Link $model
     */
    public function __construct(Link $model)
    {
        parent::__construct($model);
    }

    /**
     * Получение доступных для использования ссылок.
     * @return mixed
     */
    public function available()
    {
        return self::where('status', '=', Link::CONFIRMED)->get();
    }

    /**
     * Создание доступной ссылки.
     * @param $data
     * @return mixed
     * @throws CustomException
     * @throws ValidationException
     */
    public function store($data)
    {

        $rules = [
            'url' => 'required|url',
            'type_id' => 'required|numeric|exists:mysql_temp.types,id'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Проверка уже имеющихся ссылок на идентичность с новой в Link
        if (self::where('url', '=', $data['url'])->count())
            throw new CustomException('Данный url адрес уже имеется в системе');

        // Определение активного и доступного модератора.
        $moderator = UserService::where('role_id', '=', 1)->get()->random(1)->first();

        // Создание модели ссылки.
        return Link::create([
            'url'           => $data['url'],
            'creator_id'    => Auth::id(),
            'moderator_id'  => $moderator->id,
            'type_id'       => $data['type_id'],
            'status'        => Link::MODERATION,
        ]);
    }

    /**
     * Подтверждение ссылки.
     * @param $id
     * @param $value
     * @return mixed
     * @throws CustomException
     */
    public function confirm($id, $value)
    {
        // Получение ссылки.
        $link = self::findOrFail($id);

        // Изменение статуса.
        if (!$value) $link->status = Link::CANCELED;
        else {
            $link->status = Link::CONFIRMED;
            event(new ConfirmationLink($link));
        }

        // Сохранение.
        $link->save();
        return $link;
    }

}
