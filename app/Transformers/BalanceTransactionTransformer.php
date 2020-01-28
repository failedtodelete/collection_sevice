<?php

namespace App\Transformers;

use App\Models\BalanceTransaction;
use League\Fractal\TransformerAbstract;

class BalanceTransactionTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     * @param BalanceTransaction $history
     * @return array
     */
    public function transform(BalanceTransaction $history)
    {
        return [
            'id'        => (int) $history->id,
            'amount'    => (int) $history->amount,
            'message'   => (string) $history->message
        ];
    }
}
