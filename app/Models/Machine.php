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

    public function firstImage()
    {
        return $this->hasOne(MachineImage::class)->orderBy('sort_order');
    }

    public function featuredImage()
    {
        return $this->hasOne(MachineImage::class)->where('is_featured', true);
    }

    /**
     * Imagem principal da máquina.
     *
     * Usa a imagem marcada como principal (is_featured); se não existir,
     * faz fallback para a primeira por sort_order. Reaproveita as relações
     * já carregadas para evitar queries adicionais:
     *  - se a coleção `images` estiver carregada (página de detalhe), usa-a;
     *  - caso contrário usa as relações `featuredImage`/`firstImage`
     *    (que devem ser eager-loaded nas listagens).
     */
    public function getMainImageAttribute(): ?MachineImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images->firstWhere('is_featured', true)
                ?? $this->images->first();
        }

        return $this->featuredImage ?? $this->firstImage;
    }

    /**
     * Preço formatado para apresentação (ex.: "1.234 €") ou null quando vazio.
     */
    public function getPriceFormattedAttribute(): ?string
    {
        if ($this->price === null || $this->price === '') {
            return null;
        }

        return number_format((float) $this->price, 0, ',', '.') . ' €';
    }

    /**
     * Regra única de apresentação de preço/negociável:
     *  - 'price_negotiable': tem preço e é negociável (mostrar ambos)
     *  - 'price':            apenas preço
     *  - 'negotiable':       apenas negociável ("Preço negociável")
     *  - 'on_request':       nenhum ("Sob consulta")
     */
    public function priceState(): string
    {
        $hasPrice = $this->price !== null && $this->price !== '';
        $negotiable = (bool) $this->negotiable;

        return match (true) {
            $hasPrice && $negotiable => 'price_negotiable',
            $hasPrice => 'price',
            $negotiable => 'negotiable',
            default => 'on_request',
        };
    }

    /**
     * Rótulo do estado em português, a partir de config/machines.php.
     */
    public function getStatusLabelAttribute(): string
    {
        $status = (string) ($this->status ?? '');

        return config("machines.statuses.{$status}", ucfirst($status));
    }
}
