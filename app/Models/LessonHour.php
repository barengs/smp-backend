<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonHour extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'lesson_hours';
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'order',
        'description',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Define any relationships here if needed
    // For example, if lesson hours are associated with specific classes or subjects
}
