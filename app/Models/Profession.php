<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function parents()
    {
        return $this->hasMany(parent::class);
    }
}
