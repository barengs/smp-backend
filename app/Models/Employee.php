<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = ['id'];
    protected $table = 'employees';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
