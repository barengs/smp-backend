<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPlan extends Model
{
    protected $table = 'installment_plans';
    protected $primaryKey = 'plan_id';

    protected $fillable = [
        'account_number',
        'original_transaction_id',
        'principal_amount',
        'total_amount_due',
        'monthly_payment',
        'number_of_installments',
        'remaining_balance',
        'status',
        'start_date',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'total_amount_due' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'start_date' => 'date',
    ];

    /**
     * Get the account that owns the InstallmentPlan.
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_number', 'account_number');
    }

    /**
     * Get the original transaction that triggered the InstallmentPlan.
     */
    public function originalTransaction()
    {
        return $this->belongsTo(Transaction::class, 'original_transaction_id', 'transaction_id');
    }

    /**
     * Get all of the schedules for the InstallmentPlan.
     */
    public function schedules()
    {
        return $this->hasMany(InstallmentSchedule::class, 'plan_id', 'plan_id');
    }
}
