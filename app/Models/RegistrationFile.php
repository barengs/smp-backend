<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationFile extends Model
{
    protected $fillable = [
        'registration_id',
        'file_name',
        'file_path',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
