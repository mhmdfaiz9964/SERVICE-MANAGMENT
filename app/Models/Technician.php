<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'job_role',
        'profile_photo',
        'availability_status',
        'status',
    ];
    
}

