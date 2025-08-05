<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionalBill extends Model
{
    protected $table = 'institutional_bills';
    protected $primaryKey = 'bill_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'bill_id',
        'customer_id',
        'inst_product_id',
        'customer_ref_number',
        'amount',
        'status',
        'chosen_scheme',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Get the customer that owns the bill.
     */
    public function customer()
    {
        return $this->belongsTo(Student::class, 'customer_id', 'id');
    }

    /**
     * Get the institutional product associated with the bill.
     */
    public function institutionalProduct()
    {
        return $this->belongsTo(InstitutionalProduct::class, 'inst_product_id', 'inst_product_id');
    }
}
