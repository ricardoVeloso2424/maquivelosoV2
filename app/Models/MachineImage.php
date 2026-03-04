<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function getPublicUrlAttribute(): ?string
    {
        $path = (string) ($this->path ?? '');

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalized = ltrim($path, '/');
        if (str_starts_with($normalized, 'public/')) {
            $normalized = substr($normalized, 7);
        }

        return Storage::url($normalized);
    }
}
