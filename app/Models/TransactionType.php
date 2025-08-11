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
        'is_debit',
        'is_credit',
        'is_active',
    ];
}
