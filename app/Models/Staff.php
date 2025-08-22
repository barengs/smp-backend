<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;
    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'nik',
        'email',
        'phone',
        'address',
        'zip_code',
        'photo',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
