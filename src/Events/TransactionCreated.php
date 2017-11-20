<?php

namespace Amelia\Monzo\Events;

use Ramsey\Uuid\Uuid;
use Amelia\Monzo\Models\Transaction;
use Amelia\Monzo\Contracts\HasMonzoCredentials;

class TransactionCreated
{
    /**
     * The type of this transaction.
     *
     * @var string
     */
    public $type = 'transaction.created';

    /**
     * An ID used to prevent processing duplicates.
     *
     * @var string
     */
    public $id;

    /**
     * A user model for this transaction event.
     *
     * @var \Illuminate\Database\Eloquent\Model|\Amelia\Monzo\Contracts\HasMonzoCredentials
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
     * @param \Amelia\Monzo\Contracts\HasMonzoCredentials|\Illuminate\Database\Eloquent\Model $user
     */
    public function __construct(Transaction $transaction, HasMonzoCredentials $user)
    {
        $this->transaction = $transaction;
        $this->user = $user;
        $this->id = (string) Uuid::uuid4();
    }
}
