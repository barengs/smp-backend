<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationType extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
    ];
}
