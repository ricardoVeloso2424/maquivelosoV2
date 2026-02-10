<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Machine;
use App\Models\MachineImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MachineController extends Controller
{
    private const STATUS = ['available', 'reserved', 'sold', 'inactive'];

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $category = (string) $request->get('category', '');
        $status = (string) $request->get('status', '');

        $machines = Machine::query()
            ->with(['category', 'images'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('brand', 'like', "%{$q}%")
                        ->orWhere('model', 'like', "%{$q}%");
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category_id', $category))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();

        return view('admin.machines.index', compact('machines', 'q', 'categories', 'category', 'status'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.machines.create', [
            'machine' => new Machine(),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(self::STATUS)],
            'description' => ['nullable', 'string'],
            'featured' => ['nullable', 'boolean'],
            'negotiable' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:16144'],
        ]);

        $data['featured'] = $request->boolean('featured');
        $data['negotiable'] = $request->boolean('negotiable');

        $machine = Machine::create($data);

        $this->storeImages($machine, $request);

        return redirect()->route('admin.machines.index')->with('success', 'Máquina criada com sucesso.');
    }

    public function show(Machine $machine)
    {
        $machine->load(['category', 'images']);

        return view('admin.machines.show', compact('machine'));
    }

    public function edit(Machine $machine)
    {
        $machine->load(['category', 'images']);

        $categories = Category::query()->orderBy('name')->get();

        return view('admin.machines.edit', compact('machine', 'categories'));
    }

    public function update(Request $request, Machine $machine)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(self::STATUS)],
            'description' => ['nullable', 'string'],
            'featured' => ['nullable', 'boolean'],
            'negotiable' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:16144'],
        ]);

        $data['featured'] = $request->boolean('featured');
        $data['negotiable'] = $request->boolean('negotiable');

        $machine->update($data);

        $this->storeImages($machine, $request);

        return redirect()->route('admin.machines.index')->with('success', 'Máquina atualizada com sucesso.');
    }

    public function destroy(Machine $machine)
    {
        $machine->load('images');

        foreach ($machine->images as $img) {
            $this->deleteImageFile($img);
        }

        $machine->delete();

        return redirect()->route('admin.machines.index')->with('success', 'Máquina removida com sucesso.');
    }

    public function destroyImage(Machine $machine, MachineImage $image)
    {
        if ((int) $image->machine_id !== (int) $machine->id) {
            abort(404);
        }

        $this->deleteImageFile($image);

        $image->delete();

        return back()->with('success', 'Imagem removida com sucesso.');
    }

    public function updateStatus(Request $request, Machine $machine)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUS)],
        ]);

        $machine->update([
            'status' => $data['status'],
        ]);

        return response()->json(['ok' => true]);
    }

    private function storeImages(Machine $machine, Request $request): void
    {
        $files = $request->file('images', []);
        if (!is_array($files) || count($files) === 0) return;

        $machine->loadMissing('images');

        $nextSort = (int) ($machine->images->max('sort_order') ?? -1) + 1;

        foreach ($files as $file) {
            if (!$file) continue;

            $path = $file->store('machines', 'public');

            $machine->images()->create([
                'path' => $path,
                'sort_order' => $nextSort,
            ]);

            $nextSort++;
        }
    }

    private function deleteImageFile(MachineImage $image): void
    {
        $path = (string) ($image->path ?? '');
        if ($path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
