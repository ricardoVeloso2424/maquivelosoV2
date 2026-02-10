<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'model',
        'price',
        'status',
        'description',
        'featured',
        'negotiable',        
    ];

    protected $casts = [
        'featured' => 'boolean',
        'negotiable' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(MachineImage::class)->orderBy('sort_order');
    }

    public function featuredImage()
    {
        return $this->hasOne(MachineImage::class)->where('is_featured', true);
    }
}

