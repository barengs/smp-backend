<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionalProduct extends Model
{
    protected $table = 'institutional_products';
    protected $primaryKey = 'institutional_product_id';

    protected $fillable = [
        'partner_id',
        'product_code',
        'product_name',
        'fixed_amount',
        'available_schemes',
        'is_active',
    ];

    protected $casts = [
        'fixed_amount' => 'decimal:2',
        'available_schemes' => 'json',
        'is_active' => 'boolean',
    ];

    /**
     * Get the partner that owns the institutional product.
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    /**
     * Get all of the accounts for the Institutional Product.
     */
    public function accounts()
    {
        return $this->hasMany(Account::class, 'institutional_product_id', 'institutional_product_id');
    }

    /**
     * Get all of the transactions for the Institutional Product.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'institutional_product_id', 'institutional_product_id');
    }
}
