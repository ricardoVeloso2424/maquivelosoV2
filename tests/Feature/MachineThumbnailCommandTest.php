<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\MachineImage;
use App\Services\ThumbnailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MachineThumbnailCommandTest extends TestCase
{
    use RefreshDatabase;

    private function skipWithoutGd(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('A extensão GD não está instalada neste ambiente.');
        }
    }

    public function test_it_reports_clearly_when_gd_is_unavailable(): void
    {
        if (extension_loaded('gd')) {
            $this->markTestSkipped('GD está instalado; cenário não aplicável.');
        }

        $this->artisan('machines:generate-thumbnails')
            ->expectsOutputToContain('GD')
            ->assertExitCode(1);
    }

    public function test_it_generates_thumbnails_for_images_missing_one(): void
    {
        $this->skipWithoutGd();
        Storage::fake('public');

        $path = UploadedFile::fake()->image('wide.jpg', 800, 600)->store('machines', 'public');

        $machine = Machine::create(['name' => 'M', 'status' => 'available']);
        $image = $machine->images()->create(['path' => $path, 'sort_order' => 1]);

        $this->artisan('machines:generate-thumbnails')
            ->expectsOutputToContain('criadas: 1')
            ->assertExitCode(0);

        $image->refresh();
        $this->assertNotNull($image->thumb_path);
        Storage::disk('public')->assertExists($image->thumb_path);
    }

    public function test_it_skips_images_whose_original_file_is_missing(): void
    {
        $this->skipWithoutGd();
        Storage::fake('public');

        $machine = Machine::create(['name' => 'M', 'status' => 'available']);
        $image = $machine->images()->create(['path' => 'machines/does-not-exist.jpg', 'sort_order' => 1]);

        $this->artisan('machines:generate-thumbnails')
            ->expectsOutputToContain('ignoradas: 1')
            ->assertExitCode(0);

        $this->assertNull($image->fresh()->thumb_path);
    }

    public function test_it_does_not_regenerate_existing_thumbnails_without_force(): void
    {
        $this->skipWithoutGd();
        Storage::fake('public');

        $path = UploadedFile::fake()->image('wide.jpg', 800, 600)->store('machines', 'public');

        $machine = Machine::create(['name' => 'M', 'status' => 'available']);
        $image = $machine->images()->create([
            'path' => $path,
            'thumb_path' => 'machines/thumbs/existing.jpg',
            'sort_order' => 1,
        ]);

        $this->artisan('machines:generate-thumbnails')
            ->expectsOutputToContain('criadas: 0')
            ->assertExitCode(0);

        $this->assertSame('machines/thumbs/existing.jpg', $image->fresh()->thumb_path);

        $this->artisan('machines:generate-thumbnails', ['--force' => true])
            ->expectsOutputToContain('criadas: 1')
            ->assertExitCode(0);

        $this->assertNotSame('machines/thumbs/existing.jpg', $image->fresh()->thumb_path);
    }

    public function test_service_returns_null_for_missing_file(): void
    {
        Storage::fake('public');

        $service = app(ThumbnailService::class);

        $this->assertNull($service->generate('machines/missing.jpg'));
        $this->assertNull($service->generate(''));
    }

    public function test_service_deletes_original_and_thumbnail_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('machines/original.jpg', 'x');
        Storage::disk('public')->put('machines/thumbs/original.jpg', 'y');

        $image = new MachineImage([
            'path' => 'machines/original.jpg',
            'thumb_path' => 'machines/thumbs/original.jpg',
        ]);

        app(ThumbnailService::class)->deleteImageFiles($image);

        Storage::disk('public')->assertMissing('machines/original.jpg');
        Storage::disk('public')->assertMissing('machines/thumbs/original.jpg');
    }
}
