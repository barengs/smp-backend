<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'parent_id',
        'description',
    ];

    public function parent()
    {
        return $this->belongsTo(Hostel::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Hostel::class, 'parent_id');
    }
}
