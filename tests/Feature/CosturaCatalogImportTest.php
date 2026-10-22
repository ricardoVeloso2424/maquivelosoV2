<?php

namespace Tests\Feature;

use App\Console\Commands\ImportCosturaCatalog;
use App\Models\Category;
use App\Models\Machine;
use App\Services\CosturaCatalogParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CosturaCatalogImportTest extends TestCase
{
    use RefreshDatabase;

    private function png(int $width): string
    {
        $image = imagecreatetruecolor($width, 10);
        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }

    private function card(string $name, int $id, bool $image = true): string
    {
        return '<form class="oe_product_cart"><a itemprop="name" href="/shop/product-'.$id.'">'.$name.'</a>'.($image ? '<img itemprop="image" src="/web/image/'.$id.'"/>' : '').'<span itemprop="price" content="9876.54">9876,54 €</span></form>';
    }

    public function test_custom_placeholder_and_brand_are_recognized(): void
    {
        $this->assertTrue(CosturaCatalogParser::isPlaceholder(file_get_contents(base_path('tests/Fixtures/costura/no-photo.png')), $this->png(256)));
        $this->assertSame('Juki', CosturaCatalogParser::brand('Maquina de costura Juki DDL 8700'));
        $this->assertNull(CosturaCatalogParser::brand('Agulhas DBXK5 SAN 1 - 75'));
        $this->assertSame('Singer', CosturaCatalogParser::brand('Agulhas Singer 130 / 705HS - 90'));
    }

    public function test_names_only_lose_the_initial_code(): void
    {
        $this->assertSame('Agulhas DBXK5 [SAN 1] - 75', CosturaCatalogParser::cleanName('  [769242] Agulhas DBXK5 [SAN 1] - 75 '));
        $this->assertSame('JUKI MO-6814S', CosturaCatalogParser::cleanName('JUKI MO-6814S'));
    }

    public function test_import_paginates_skips_missing_photos_and_is_idempotent(): void
    {
        Storage::fake('public');
        Http::preventStrayRequests();
        Category::create(['name' => 'Agulhas']);
        $placeholder = $this->png(256);
        $photo = $this->png(800);
        $routes = [
            'www.costura.pt/web/image/product.template/0/image_1920' => Http::response($placeholder),
            'www.costura.pt/shop/product-*' => Http::response('<img class="product_detail_img" data-zoom-image="/web/image/photo"/>'),
            'www.costura.pt/web/image/photo' => Http::response($photo),
        ];
        foreach (ImportCosturaCatalog::SOURCES as $category => [$slug]) {
            $path = '/shop/category/'.$slug;
            $routes['www.costura.pt'.$path] = Http::response($this->card('[123] Máquina '.$category, 1).$this->card('[456] Sem imagem', 2, false).'<a class="page-link" href="'.$path.'/page/2">2</a>');
            $routes['www.costura.pt'.$path.'/page/2'] = Http::response($this->card('[123] Máquina '.$category, 1).$this->card('[789] Máquina segunda '.$category, 3).'<a class="page-link" href="'.$path.'">1</a>');
        }
        Http::fake($routes);
        $this->artisan('catalog:import-costura', ['--delay' => 0])->assertSuccessful();
        $this->assertSame(6, Machine::count());
        $this->assertSame(3, Category::count());
        foreach (Machine::with('images', 'category')->get() as $machine) {
            $this->assertNull($machine->price);
            $this->assertTrue($machine->negotiable);
            $this->assertFalse($machine->featured);
            $this->assertSame('available', $machine->status);
            $this->assertStringContainsString($machine->category->name, $machine->name);
            $this->assertTrue($machine->main_image->is_featured);
            Storage::disk('public')->assertExists($machine->main_image->path);
            Storage::disk('public')->assertExists($machine->main_image->thumb_path);
            $this->get('/catalogo/'.$machine->id)->assertOk()->assertSee('Preço negociável');
        }
        $before = count(Http::recorded());
        $this->artisan('catalog:import-costura', ['--delay' => 0])->assertSuccessful();
        $this->assertSame(6, Machine::count());
        // Second run requests only the placeholder and listing pages, never product photos.
        $this->assertSame(7, count(Http::recorded()) - $before);
    }

    public function test_placeholder_and_failed_download_are_not_imported(): void
    {
        Storage::fake('public');
        $placeholder = $this->png(256);
        Http::fake(function ($request) use ($placeholder) {
            if (str_contains($request->url(), '/shop/category/')) {
                return Http::response($this->card('Máquina placeholder', 1).$this->card('Máquina falha', 2));
            }
            if (str_contains($request->url(), 'product-1')) {
                return Http::response('<img class="product_detail_img" src="/web/image/missing-photo"/>');
            }
            if (str_contains($request->url(), 'product-2')) {
                return Http::response('error', 503);
            }

            return Http::response($placeholder);
        });
        $this->artisan('catalog:import-costura', ['--delay' => 0])->assertFailed();
        $this->assertSame(0, Machine::count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_twenty_existing_products_satisfy_limit_without_importing_more(): void
    {
        Storage::fake('public');
        $placeholder = $this->png(256);
        Http::fake(function ($request) use ($placeholder) {
            if (str_contains($request->url(), '/web/image/')) {
                return Http::response($placeholder);
            }
            if (str_contains($request->url(), 'costura-agulha-86')) {
                return Http::response($this->card('Agulha sem imagem', 99, false));
            }
            $cards = '';
            for ($i = 1; $i <= 21; $i++) {
                $cards .= $this->card('Máquina '.$i, $i);
            }

            return Http::response($cards);
        });
        foreach (ImportCosturaCatalog::SOURCES as $categoryName => [$slug, $limit]) {
            if (! $limit) {
                continue;
            }
            $category = Category::create(['name' => $categoryName]);
            for ($i = 1; $i <= 20; $i++) {
                $machine = Machine::create(['name' => 'Máquina '.$i, 'category_id' => $category->id]);
                Storage::disk('public')->put('machines/'.$machine->id.'.png', $placeholder);
                $machine->images()->create(['path' => 'machines/'.$machine->id.'.png']);
            }
        }
        $this->artisan('catalog:import-costura', ['--delay' => 0])->assertSuccessful();
        $this->assertSame(40, Machine::count());
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/shop/product-21'));
    }
}
