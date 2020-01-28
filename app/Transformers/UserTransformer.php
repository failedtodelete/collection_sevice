<?php namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'permissions', 'balance_transactions'
    ];

    protected $defaultIncludes = [
        'role', 'status'
    ];

    /**
     * A Fractal transformer.
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'        => (int) $user->id,
            'name'      => (string) $user->name,
            'email'     => (string) $user->email,
            'balance'   => (int) $user->balance,
            'paid_out'  => (int) $user->paid_out
        ];
    }

    /**
     * Получение разрешений.
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includePermissions(User $user)
    {
        return $this->collection($user->permissions, new PermissionTransformer());
    }

    /**
     * Получение роли пользователя.
     * @param User $user
     * @return \League\Fractal\Resource\Item
     */
    public function includeRole(User $user)
    {
        return $this->item($user->role, new RoleTransformer());
    }

    /**
     * Получение истории баланса пользователя.
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeBalanceTransactions(User $user)
    {
        return $this->collection($user->balance_transactions, new BalanceTransactionTransformer());
    }

    /**
     * Получение статуса текущего пользователя.
     * @param User $user
     * @return \League\Fractal\Resource\Item
     */
    public function includeStatus(User $user)
    {
        return $this->item($user->status, new UserStatusTransformer());
    }

}
