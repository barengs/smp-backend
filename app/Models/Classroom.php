<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'parent_id',
        'description',
    ];

    // public function students()
    // {
    //     return $this->hasMany(Student::class);
    // }

    // public function teachers()
    // {
    //     return $this->hasMany(Teacher::class);
    // }
}
