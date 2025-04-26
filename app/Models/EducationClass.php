<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationClass extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'code',
        'name',
    ];

    public function education()
    {
        return $this->belongsToMany(Education::class, 'education_has_education_classes', 'education_class_id', 'education_id');
    }
}
