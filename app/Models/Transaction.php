<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'transaction_type',
        'description',
        'amount',
        'status',
        'reference_number',
        'channel',
        'source_account',
        'destination_account',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the source account for the Transaction.
     */
    public function sourceAccount()
    {
        return $this->belongsTo(Account::class, 'source_account', 'account_number');
    }

    /**
     * Get the destination account for the Transaction.
     */
    public function destinationAccount()
    {
        return $this->belongsTo(Account::class, 'destination_account', 'account_number');
    }

    /**
     * Get all of the ledger entries for the Transaction.
     */
    public function ledgerEntries()
    {
        return $this->hasMany(TransactionLedger::class, 'transaction_id', 'id');
    }
}
