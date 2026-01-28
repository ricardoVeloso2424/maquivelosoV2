<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineImage extends Model
{
    protected $fillable = ['machine_id', 'path', 'sort_order', 'is_featured'];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
