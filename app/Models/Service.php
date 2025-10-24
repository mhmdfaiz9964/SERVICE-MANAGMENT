<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_category_id',
        'name',
        'slug',
        'description',
        'base_price',
        'duration_minutes',
        'metadata',
        'status',
    ];

    protected $casts = [
        'metadata' => 'array',
        'base_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}
