<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    protected $guarded = ['id'];

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'staff_study', 'study_id', 'staff_id');
    }

    public function staffStudies()
    {
        return $this->hasMany(StaffStudy::class);
    }
}
