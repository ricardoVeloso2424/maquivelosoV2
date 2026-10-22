<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Machine;
use App\Services\CosturaCatalogParser;
use App\Services\ThumbnailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ImportCosturaCatalog extends Command
{
    protected $signature = 'catalog:import-costura {--delay=500 : Pausa entre pedidos em milissegundos (mínimo 250 fora de testes)}';

    protected $description = 'Importa produtos com fotografia da costura.pt, sem preços e com imagens locais.';

    public const SOURCES = [
        'Corte e Cose' => ['costura-corte-e-cose-76', 20],
        'Ponto Preso' => ['costura-ponto-preso-77', 20],
        'Agulhas' => ['costura-agulha-86', null],
    ];

    private array $stats = ['sem imagem' => 0, 'duplicação' => 0, 'falhas de download' => 0, 'inválidos' => 0];

    private int $delay;

    private function fetch(string $url): string
    {
        // Guzzle response/handler cycles can otherwise accumulate during long imports.
        gc_collect_cycles();
        usleep($this->delay * 1000);
        $response = Http::withUserAgent('MaquiVeloso-Catalog-Importer/1.0')->withHeaders(['Accept-Language' => 'pt-PT,pt;q=0.9'])->connectTimeout(10)->timeout(40)
            ->withOptions(['allow_redirects' => false])->get($url)->throw();
        if (! $response->successful()) {
            throw new RuntimeException('Resposta HTTP inesperada: '.$response->status());
        }

        return $response->body();
    }

    public function handle(CosturaCatalogParser $parser, ThumbnailService $thumbnails): int
    {
        $this->delay = max(app()->environment('testing') ? 0 : 250, (int) $this->option('delay'));
        $lock = Cache::lock('catalog-import-costura', 86400);
        if (! $lock->get()) {
            $this->error('Já existe uma importação em curso.');

            return self::FAILURE;
        }
        $failed = false;
        if (! Schema::hasColumn('machine_images', 'thumb_path')) {
            $this->error('Aplicar a migration existente de thumb_path antes de importar.');
            $lock->release();

            return self::FAILURE;
        }
        try {
            // Odoo returns a real PNG placeholder even for nonexistent product IDs.
            $placeholder = $this->fetch(CosturaCatalogParser::ORIGIN.'/web/image/product.template/0/image_1920');
            if (! @getimagesizefromstring($placeholder)) {
                throw new RuntimeException('Não foi possível identificar o placeholder da fonte.');
            }
            foreach (self::SOURCES as $categoryName => [$slug, $limit]) {
                $category = Category::query()->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($categoryName)])->first()
                    ?? Category::create(['name' => $categoryName]);
                $categoryPath = '/shop/category/'.$slug;
                $pending = [CosturaCatalogParser::ORIGIN.$categoryPath];
                $visited = $seen = [];
                $eligible = $imported = 0;
                $complete = true;
                while ($pending && ($limit === null || $eligible < $limit)) {
                    $url = array_shift($pending);
                    if (isset($visited[$url])) {
                        continue;
                    }
                    if (count($visited) >= 500) {
                        $complete = false;
                        $this->error('Limite de segurança de páginas atingido.');
                        break;
                    }
                    $visited[$url] = true;
                    $this->info("[$categoryName] página ".count($visited).'...');
                    try {
                        $listing = $parser->listing($this->fetch($url), $categoryPath);
                    } catch (Throwable $e) {
                        $this->warn($e->getMessage());
                        $complete = false;

                        continue;
                    }
                    foreach ($listing['pages'] as $page) {
                        if (! isset($visited[$page]) && ! in_array($page, $pending, true)) {
                            $pending[] = $page;
                        }
                    }
                    foreach ($listing['products'] as $product) {
                        if ($limit !== null && $eligible >= $limit) {
                            break;
                        }
                        $name = $product['name'];
                        if (isset($seen[$name])) {
                            $this->stats['duplicação']++;

                            continue;
                        }
                        $seen[$name] = true;
                        if ($name === '' || mb_strlen($name) > 255) {
                            $this->stats['inválidos']++;

                            continue;
                        }
                        // Machine categories occasionally contain spare parts: do not count them as machines.
                        if ($limit !== null && ! preg_match('/m[aá]quina/iu', $name)) {
                            $this->stats['inválidos']++;

                            continue;
                        }
                        $existing = Machine::where('category_id', $category->id)->where('name', $name)->first();
                        if ($existing) {
                            $this->stats['duplicação']++;
                            if ($existing->images->contains(fn ($image) => Storage::disk('public')->exists($image->path))) {
                                $eligible++;
                            }

                            continue;
                        }
                        if (! $product['has_image']) {
                            $this->stats['sem imagem']++;

                            continue;
                        }
                        $path = $thumb = null;
                        try {
                            $imageUrl = $parser->image($this->fetch($product['url']));
                            if (! $imageUrl) {
                                $this->stats['sem imagem']++;

                                continue;
                            }
                            $bytes = $this->fetch($imageUrl);
                            if (CosturaCatalogParser::isPlaceholder($bytes, $placeholder)) {
                                $this->stats['sem imagem']++;

                                continue;
                            }
                            $info = @getimagesizefromstring($bytes);
                            $extension = match ($info[2] ?? null) {
                                IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif', default => null
                            };
                            if (! $extension || strlen($bytes) > 5 * 1024 * 1024) {
                                throw new RuntimeException('Imagem inválida ou superior a 5 MB.');
                            }
                            $path = 'machines/costura-'.Str::uuid().'.'.$extension;
                            if (! Storage::disk('public')->put($path, $bytes)) {
                                throw new RuntimeException('Falha ao guardar imagem.');
                            }
                            $thumb = $thumbnails->generate($path);
                            DB::transaction(function () use ($category, $name, $path, $thumb) {
                                $machine = Machine::create([
                                    'category_id' => $category->id, 'name' => $name, 'brand' => CosturaCatalogParser::brand($name), 'model' => null,
                                    'description' => null, 'price' => null, 'negotiable' => true,
                                    'status' => 'available', 'featured' => false,
                                ]);
                                $machine->images()->create(['path' => $path, 'thumb_path' => $thumb, 'sort_order' => 0, 'is_featured' => true]);
                            });
                            $imported++;
                            $eligible++;
                            $this->line("[$categoryName] $eligible/".($limit ?? 'todos')." — $name");
                        } catch (Throwable $e) {
                            Storage::disk('public')->delete(array_filter([$path, $thumb]));
                            $this->stats['falhas de download']++;
                            $complete = false;
                            $this->warn("Ignorado: $name — ".$e->getMessage());
                        }
                    }
                }
                $this->info("$categoryName: $imported importadas; $eligible elegíveis/".($limit ?? 'todos').'; '.count($visited).' páginas');
                if ($limit !== null && $eligible < $limit) {
                    $this->warn("Apenas $eligible/$limit elegíveis encontrados nesta execução.");
                }
                if (! $complete) {
                    $failed = true;
                    $this->warn('Ocorreram falhas; voltar a executar para completar a categoria.');
                }
            }
            foreach ($this->stats as $reason => $count) {
                $this->line("Ignoradas — $reason: $count");
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            $failed = true;
        } finally {
            $lock->release();
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
