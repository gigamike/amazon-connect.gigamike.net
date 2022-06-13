<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactLogs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contact_id',
        'contact_initiation_method',
        'customer_endpoint_address',
        'system_endpoint_address',
        'customer_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];
}
