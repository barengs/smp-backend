<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'hostel_id',
        'capacity',
        'description',
        'is_active',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }
}
