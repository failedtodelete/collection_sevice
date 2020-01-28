<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Models\User;

class UserService extends BaseService
{

    /**
     * UserService constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Создание пользователя.
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function store($data)
    {

        $rules = [
            'name'      => 'required|string',
            'email'     => 'required|string|unique:mysql_admin.users,email',
            'password'  => 'required|string|min:6',
            'role_id'   => 'required|numeric|exists:mysql_admin.roles,id'
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);

        // Создание пользователя.
        return User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role_id'   => $data['role_id'],
            'status_id' => 1
        ]);
    }

    /**
     * Оплата услуг пользователю.
     * @param $user_id
     * @param $amount
     * @param $message
     * @throws \App\Exceptions\CustomException
     */
    public function service_payment($user_id, $amount, $message)
    {
        // Получение пользователя.
        $user = self::findOrFail($user_id);

        try {

            DB::beginTransaction();

            // Создание записи в баланасе пользователя и начисление ему средств на баланс.
            $user->balance_transactions()->create(['amount' => $amount, 'message' => $message]);
            $user->balance += $amount;
            $user->save();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
    }

}
