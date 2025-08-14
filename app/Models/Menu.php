<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'title',
        'description',
        'icon',
        'route',
        'parent_id',
        'type',
        'position',
        'status',
        'order',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function child()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order', 'asc');
    }
}
