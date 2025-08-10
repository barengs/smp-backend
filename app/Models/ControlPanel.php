<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlPanel extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'app_version',
        'app_description',
        'app_logo',
        'app_favicon',
        'app_url',
        'app_email',
        'app_phone',
        'app_address',
        'is_maintenance_mode',
        'maintenance_message',
        'app_theme',
        'app_language',
    ];
}
