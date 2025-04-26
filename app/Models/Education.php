<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $guarded = ['id'];
    protected $table = 'educations';
    protected $fillable = [
        'name',
        'description',
    ];

    public function education_class()
    {
        return $this->belongsToMany(EducationClass::class, 'education_has_education_classes', 'education_id', 'education_class_id');
    }
}
