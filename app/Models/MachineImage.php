<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MachineImage extends Model
{
    protected $fillable = ['machine_id', 'path', 'thumb_path', 'sort_order', 'is_featured'];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * URL pública do ficheiro original (usada na página de detalhe).
     */
    public function getPublicUrlAttribute(): ?string
    {
        return $this->urlFor($this->path);
    }

    /**
     * URL da miniatura (usada em listagens: catálogo, home, backoffice).
     * Faz fallback para o original quando não existe miniatura gerada.
     */
    public function getThumbUrlAttribute(): ?string
    {
        return $this->urlFor($this->thumb_path) ?? $this->public_url;
    }

    /**
     * Normaliza um caminho de imagem para uma URL pública.
     */
    protected function urlFor(?string $path): ?string
    {
        $path = (string) ($path ?? '');

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
