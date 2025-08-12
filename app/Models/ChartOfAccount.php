<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $primaryKey = 'coa_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'coa_code',
        'account_name',
        'account_type',
        'parent_coa_code',
        'is_postable',
        'is_active',
    ];

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_coa_code', 'coa_code');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_coa_code', 'coa_code');
    }
}
