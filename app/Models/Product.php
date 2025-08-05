<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_code',
        'product_name',
        'product_type',
        'interest_rate',
        'admin_fee',
        'is_active',
    ];

    protected $casts = [
        'interest_rate' => 'decimal:4',
        'admin_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get all of the accounts for the Product.
     */
    public function accounts()
    {
        return $this->hasMany(Account::class, 'product_id', 'id');
    }
}
