<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLedger extends Model
{
    protected $table = 'transaction_ledgers';
    protected $primaryKey = 'id';
    public $timestamps = false; // Karena tidak ada kolom created_at/updated_at di migrasi

    protected $fillable = [
        'transaction_id',
        'coa_code',
        'entry_type',
        'amount',
        'entry_time',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_time' => 'datetime',
    ];

    /**
     * Get the transaction that owns the ledger entry.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    /**
     * Get the chart of account that owns the ledger entry.
     */
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_code', 'coa_code');
    }
}
