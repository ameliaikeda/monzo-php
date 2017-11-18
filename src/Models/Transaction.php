<?php

namespace Amelia\Monzo\Models;

/**
 * Transaction model.
 *
 * @property  string               $id                  The ID of this transaction.
 * @property  \Carbon\Carbon       $created             The date this transaction was created.
 * @property  string               $description         A description of this transaction.
 * @property  int                  $amount              The amount of this transaction, in pence, cents, etc.
 * @property  string               $currency            ISO4217 currency code.
 * @property  Merchant|string      $merchant            The merchant for this transaction.
 * @property  string               $notes               A description of this transaction (The "notes" field from metadata).
 * @property  array                $metadata            An arbitrary metadata array.
 * @property  int                  $account_balance     Account balance. In the current account, completely useless.
 * @property  array                $attachments         An array of attachments on this transaction.
 * @property  string               $category            The category of this transaction.
 * @property  bool                 $is_load             If this is an account topup (via card, etc).
 * @property  \Carbon\Carbon|null  $settled             If this transaction has settled, the settlement date.
 * @property  int                  $local_amount        The local amount of this transaction, if different (tends to be current GBP value at the time).
 * @property  string               $local_currency      The local currency of this transaction (ISO4217)
 * @property  \Carbon\Carbon|null  $updated             A date this transaction was updated, or null.
 * @property  string               $account_id          The account this transaction applies to.
 * @property  array                $counterparty        An array of information about the other end of this transaction.
 * @property  string               $scheme              The scheme this
 * @property  string               $dedupe_id           An ID to prevent duplicates.
 * @property  bool                 $originator          N/A
 * @property  bool                 $include_in_spending Include this transaction in the "spending" tab.
 * @property  bool                 $pending             If this transaction is pending.
 * @property  bool                 $declined            If this transaction was declined.
 * @property  string|null          $decline_reason      A reason for declining, if present.
 */
class Transaction extends Model
{
    /**
     * Casts for this model.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'date',
        'updated' => 'date',
        'settled' => 'date',
        'merchant' => Merchant::class,
    ];

    /**
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = [
        'pending',
        'declined',
    ];

    /**
     * Checks if this transaction is currently pending.
     *
     * @return bool
     */
    public function getPendingAttribute()
    {
        return $this->settled === null;
    }

    /**
     * Checks if this transaction is currently pending.
     *
     * @return bool
     */
    public function getDeclinedAttribute()
    {
        return $this->decline_reason !== null;
    }
}
