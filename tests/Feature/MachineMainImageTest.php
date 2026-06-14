<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\MachineImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineMainImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_image_falls_back_to_first_by_sort_order(): void
    {
        $machine = Machine::create(['name' => 'M', 'status' => 'available']);
        $machine->images()->create(['path' => 'machines/b.jpg', 'sort_order' => 2]);
        $first = $machine->images()->create(['path' => 'machines/a.jpg', 'sort_order' => 1]);

        $this->assertNotNull($machine->main_image);
        $this->assertSame($first->id, $machine->main_image->id);
    }

    public function test_main_image_uses_featured_when_set(): void
    {
        $machine = Machine::create(['name' => 'M', 'status' => 'available']);
        $machine->images()->create(['path' => 'machines/a.jpg', 'sort_order' => 1]);
        $featured = $machine->images()->create(['path' => 'machines/b.jpg', 'sort_order' => 2, 'is_featured' => true]);

        $this->assertSame($featured->id, $machine->main_image->id);
    }

    public function test_setting_featured_keeps_only_one_principal(): void
    {
        $machine = Machine::create(['name' => 'M', 'status' => 'available']);
        $img1 = $machine->images()->create(['path' => 'machines/a.jpg', 'sort_order' => 1, 'is_featured' => true]);
        $img2 = $machine->images()->create(['path' => 'machines/b.jpg', 'sort_order' => 2]);

        $this->actingAs($this->adminUser())
            ->patch(route('admin.machines.images.feature', ['machine' => $machine->id, 'image' => $img2->id]))
            ->assertRedirect();

        $this->assertFalse((bool) $img1->fresh()->is_featured);
        $this->assertTrue((bool) $img2->fresh()->is_featured);
        $this->assertSame(
            1,
            MachineImage::query()
                ->where('machine_id', $machine->id)
                ->where('is_featured', true)
                ->count()
        );
    }

    public function test_feature_rejects_image_from_another_machine(): void
    {
        $machineA = Machine::create(['name' => 'A', 'status' => 'available']);
        $machineB = Machine::create(['name' => 'B', 'status' => 'available']);
        $imageB = $machineB->images()->create(['path' => 'machines/b.jpg', 'sort_order' => 1]);

        $this->actingAs($this->adminUser())
            ->patch(route('admin.machines.images.feature', ['machine' => $machineA->id, 'image' => $imageB->id]))
            ->assertNotFound();
    }

    public function test_feature_route_requires_admin(): void
    {
        $machine = Machine::create(['name' => 'M', 'status' => 'available']);
        $image = $machine->images()->create(['path' => 'machines/a.jpg', 'sort_order' => 1]);

        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->patch(route('admin.machines.images.feature', ['machine' => $machine->id, 'image' => $image->id]))
            ->assertForbidden();
    }

    private function adminUser(): User
    {
        return User::allowAdminPromotion(fn () => User::factory()->create(['is_admin' => true]));
    }
}
