<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';
    protected $primaryKey = 'id';
    public $incrementing = true; // Assuming 'id' is an auto-incrementing integer

    protected $fillable = [
        'partner_code',
        'name',
        'contact_email',
        'contact_phone',
        'address',
        'logo',
        'website',
        'contact_person',
    ];

    /**
     * Get all of the institutional products for the Partner.
     */
    public function institutionalProducts()
    {
        return $this->hasMany(InstitutionalProduct::class, 'partner_id', 'id');
    }
}
