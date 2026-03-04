<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminMachineUploadValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_machine_store_rejects_too_many_images(): void
    {
        $images = [];
        for ($i = 0; $i < 9; $i++) {
            $images[] = UploadedFile::fake()->create("img-{$i}.jpg", 200, 'image/jpeg');
        }

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.machines.store'), [
                'name' => 'Machine With Too Many Images',
                'status' => 'available',
                'images' => $images,
            ]);

        $response->assertSessionHasErrors('images');
    }

    public function test_machine_store_rejects_oversized_image(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.machines.store'), [
                'name' => 'Machine With Big Image',
                'status' => 'available',
                'images' => [
                    UploadedFile::fake()->create('big.jpg', 6000, 'image/jpeg'),
                ],
            ]);

        $response->assertSessionHasErrors('images.0');
    }

    private function adminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }
}
