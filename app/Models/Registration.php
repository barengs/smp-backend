<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $table = 'registrations';
    protected $guarded = ['id'];


    // public function student()
    // {
    //     return $this->belongsTo(Student::class);
    // }

    public function parent()
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id', 'nik');
    }

    public function files()
    {
        return $this->hasMany(RegistrationFile::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id');
    }
}
