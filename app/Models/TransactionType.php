<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'default_credit_coa',
        'default_debit_coa',
        'is_debit',
        'is_credit',
        'is_active',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'transaction_type_id', 'id');
    }
}
