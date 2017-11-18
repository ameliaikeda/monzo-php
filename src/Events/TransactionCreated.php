<?php

namespace Amelia\Monzo\Events;

use Amelia\Monzo\Models\Transaction;

class TransactionCreated
{
    /**
     * The type of this transaction.
     *
     * @var string
     */
    public $type = 'transaction.created';

    /**
     * A user model for this transaction event.
     *
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    public $user;

    /**
     * The transaction sent in this event.
     *
     * @var \Amelia\Monzo\Models\Transaction
     */
    public $transaction;

    /**
     * TransactionCreated constructor.
     *
     * @param \Amelia\Monzo\Models\Transaction $transaction
     * @param \Illuminate\Database\Eloquent\Model|null $user
     */
    public function __construct(Transaction $transaction, $user = null)
    {
        $this->transaction = $transaction;
        $this->user = $user;
    }
}
