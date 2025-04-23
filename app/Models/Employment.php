<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
    ];
}
