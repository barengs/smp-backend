<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'program_id',
        'description',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
