<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentSchedule extends Model
{
    protected $table = 'installment_schedules';
    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'plan_id',
        'due_date',
        'amount_due',
        'status',
        'payment_date',
        'payment_transaction_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount_due' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the plan that owns the InstallmentSchedule.
     */
    public function plan()
    {
        return $this->belongsTo(InstallmentPlan::class, 'plan_id', 'plan_id');
    }

    /**
     * Get the payment transaction for the InstallmentSchedule.
     */
    public function paymentTransaction()
    {
        return $this->belongsTo(Transaction::class, 'payment_transaction_id', 'transaction_id');
    }
}
