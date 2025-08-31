<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffStudy extends Model
{
    protected $fillable = [
        'staff_id',
        'study_id',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }
}
