<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'meta_name',
        'meta_tag',
        'description',
        'social_links',
        'logo',
        'copyright_message',
        'splash_screen_image',
        'splash_screen_title',
        'splash_screen_description',
        'home_banner',
    ];

    protected $casts = [
        'social_links' => 'array',
        'home_banner' => 'array',
    ];
}
