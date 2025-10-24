<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'mobile_number',
        'profile_pic',
        'whatsapp_number',
    ];
}
