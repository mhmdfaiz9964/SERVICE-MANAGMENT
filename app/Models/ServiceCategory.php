<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'service_category_id');
    }
}
