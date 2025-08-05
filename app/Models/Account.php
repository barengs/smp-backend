<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'account_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'account_number',
        'customer_id',
        'product_id',
        'balance',
        'status',
        'open_date',
        'close_date',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'open_date' => 'date',
        'close_date' => 'date',
    ];

    /**
     * Get the customer that owns the Account.
     */
    public function customer()
    {
        return $this->belongsTo(Student::class, 'customer_id', 'id');
    }

    /**
     * Get the product that defines the Account.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get all of the movements for the Account.
     */
    public function movements()
    {
        return $this->hasMany(AccountMovement::class, 'account_number', 'account_number');
    }
}
