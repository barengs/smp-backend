<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
