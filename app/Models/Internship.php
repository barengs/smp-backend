<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'partner_id',
        'supervisor_id',
        'start_date',
        'end_date',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(InternshipSupervisor::class, 'supervisor_id');
    }
}
